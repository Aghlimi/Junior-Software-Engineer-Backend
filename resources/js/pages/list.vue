<script setup>
import { onMounted, ref, watch } from 'vue';
import { getCategories, getProducts } from '../api/productApi';

const props = defineProps({
	reloadToken: {
		type: Number,
		default: 0,
	},
});

const filters = ref({
	sortName: '',
	sortPrice: '',
	category: '',
	limit: 10,
});

const categories = ref([]);
const products = ref([]);
const pagination = ref({
	current_page: 1,
	last_page: 1,
	total: 0,
	from: 0,
	to: 0,
});
const loading = ref(false);
const error = ref('');
const imageLoadFailed = ref({});

function resolveImageUrl(imagePath) {
	if (!imagePath) {
		return '';
	}

	if (imagePath.startsWith('http://') || imagePath.startsWith('https://') || imagePath.startsWith('/')) {
		return imagePath;
	}

	return `/storage/${imagePath}`;
}

function markImageAsFailed(productId) {
	imageLoadFailed.value = {
		...imageLoadFailed.value,
		[productId]: true,
	};
}

async function loadCategories() {
	try {
		categories.value = await getCategories();
	} catch {
		categories.value = [];
	}
}

async function loadProducts(page = 1) {
	loading.value = true;
	error.value = '';

	try {
		const response = await getProducts({
			page,
			limit: filters.value.limit,
			sortName: filters.value.sortName,
			sortPrice: filters.value.sortPrice,
			category: filters.value.category,
		});

		products.value = response.data ?? [];
		imageLoadFailed.value = {};
		pagination.value = {
			current_page: response.current_page ?? 1,
			last_page: response.last_page ?? 1,
			total: response.total ?? 0,
			from: response.from ?? 0,
			to: response.to ?? 0,
		};
	} catch (err) {
		error.value = err.message || 'Failed to fetch products.';
		products.value = [];
	} finally {
		loading.value = false;
	}
}

function changePage(page) {
	if (page < 1 || page > pagination.value.last_page || page === pagination.value.current_page) {
		return;
	}

	loadProducts(page);
}

onMounted(async () => {
	await loadCategories();
	await loadProducts(1);
});

watch(
	() => [filters.value.sortName, filters.value.sortPrice, filters.value.category, filters.value.limit],
	() => {
		loadProducts(1);
	}
);

watch(
	() => props.reloadToken,
	() => {
		loadProducts(1);
	}
);
</script>

<template>
	<section class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
		<h2 class="text-xl font-semibold text-neutral-900">Products</h2>
		<p class="mt-1 text-sm text-neutral-500">Browse products with sorting, filtering, and pagination.</p>

		<div class="mt-5 grid gap-3 md:grid-cols-4">
			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Sort by name</span>
				<select
					v-model="filters.sortName"
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				>
					<option value="">Default</option>
					<option value="asc">A-Z</option>
					<option value="desc">Z-A</option>
				</select>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Sort by price</span>
				<select
					v-model="filters.sortPrice"
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				>
					<option value="">Default</option>
					<option value="asc">Low to high</option>
					<option value="desc">High to low</option>
				</select>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Filter by category</span>
				<select
					v-model="filters.category"
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				>
					<option value="">All categories</option>
					<option v-for="category in categories" :key="category.id" :value="String(category.id)">
						{{ category.name }}
					</option>
				</select>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Per page</span>
				<select
					v-model.number="filters.limit"
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				>
					<option :value="5">5</option>
					<option :value="10">10</option>
					<option :value="20">20</option>
				</select>
			</label>
		</div>

		<p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
		<p v-if="loading" class="mt-4 text-sm text-neutral-500">Loading products...</p>

		<div v-if="!loading" class="mt-5 overflow-x-auto">
			<table class="min-w-full border-collapse">
				<thead>
					<tr class="border-b border-neutral-200 text-left text-sm text-neutral-500">
						<th class="px-3 py-2">Image</th>
						<th class="px-3 py-2">Name</th>
						<th class="px-3 py-2">Price</th>
						<th class="px-3 py-2">Description</th>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="product in products"
						:key="product.id"
						class="border-b border-neutral-100 text-sm text-neutral-800"
					>
						<td class="px-3 py-3">
							<img
								v-if="product.image && !imageLoadFailed[product.id]"
								:src="resolveImageUrl(product.image)"
								:alt="product.name"
								class="h-12 w-12 rounded-lg object-cover"
								loading="lazy"
								@error="markImageAsFailed(product.id)"
							/>
							<div
								v-else
								class="flex h-12 w-12 items-center justify-center rounded-lg bg-neutral-100 text-xs text-neutral-500"
							>
								No image
							</div>
						</td>
						<td class="px-3 py-3 font-medium">{{ product.name }}</td>
						<td class="px-3 py-3">${{ Number(product.price).toFixed(2) }}</td>
						<td class="px-3 py-3">{{ product.description || '-' }}</td>
					</tr>
					<tr v-if="products.length === 0">
						<td colspan="4" class="px-3 py-6 text-center text-sm text-neutral-500">No products found.</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt-4 flex flex-col items-start justify-between gap-3 text-sm md:flex-row md:items-center">
			<p class="text-neutral-500">
				Showing {{ pagination.from || 0 }}-{{ pagination.to || 0 }} of {{ pagination.total || 0 }} products
			</p>

			<div class="flex items-center gap-2">
				<button
					class="rounded-lg border border-neutral-300 px-3 py-1.5 transition hover:bg-neutral-100 disabled:opacity-50"
					:disabled="pagination.current_page <= 1 || loading"
					@click="changePage(pagination.current_page - 1)"
				>
					Previous
				</button>

				<span class="text-neutral-600">Page {{ pagination.current_page }} / {{ pagination.last_page }}</span>

				<button
					class="rounded-lg border border-neutral-300 px-3 py-1.5 transition hover:bg-neutral-100 disabled:opacity-50"
					:disabled="pagination.current_page >= pagination.last_page || loading"
					@click="changePage(pagination.current_page + 1)"
				>
					Next
				</button>
			</div>
		</div>
	</section>
</template>