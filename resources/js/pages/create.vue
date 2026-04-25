<script setup>
import { onMounted, ref } from 'vue';
import { createProduct, getCategories } from '../api/productApi';

const emit = defineEmits(['created']);

const form = ref({
	name: '',
	price: '',
	description: '',
	categories: [],
	image: null,
});

const categories = ref([]);
const isSubmitting = ref(false);
const error = ref('');
const success = ref('');

function onImageChange(event) {
	const [file] = event.target.files || [];
	form.value.image = file || null;
}

function resetForm() {
	form.value = {
		name: '',
		price: '',
		description: '',
		categories: [],
		image: null,
	};
}

async function submitForm() {
	error.value = '';
	success.value = '';

	if (!form.value.image) {
		error.value = 'Image is required.';
		return;
	}

	isSubmitting.value = true;

	try {
		await createProduct({
			name: form.value.name,
			price: form.value.price,
			description: form.value.description,
			categories: form.value.categories,
			image: form.value.image,
		});

		success.value = 'Product created successfully.';
		resetForm();
		emit('created');
	} catch (err) {
		error.value = err.message || 'Failed to create product.';
	} finally {
		isSubmitting.value = false;
	}
}

onMounted(async () => {
	try {
		categories.value = await getCategories();
	} catch {
		categories.value = [];
	}
});
</script>

<template>
	<section class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
		<h2 class="text-xl font-semibold text-neutral-900">Create Product</h2>
		<p class="mt-1 text-sm text-neutral-500">Add a new product with image and categories.</p>

		<form class="mt-6 grid gap-4" @submit.prevent="submitForm">
			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Name</span>
				<input
					v-model="form.name"
					type="text"
					required
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				/>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Price</span>
				<input
					v-model="form.price"
					type="number"
					min="0"
					step="0.01"
					required
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				/>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Description</span>
				<textarea
					v-model="form.description"
					rows="3"
					class="rounded-xl border border-neutral-300 px-3 py-2 outline-none transition focus:border-blue-500"
				/>
			</label>

			<label class="grid gap-1">
				<span class="text-sm font-medium text-neutral-700">Image</span>
				<input
					type="file"
					accept="image/*"
					required
					class="rounded-xl border border-neutral-300 px-3 py-2 text-sm"
					@change="onImageChange"
				/>
			</label>

			<div class="grid gap-2">
				<span class="text-sm font-medium text-neutral-700">Categories</span>
				<div class="max-h-40 space-y-2 overflow-y-auto rounded-xl border border-neutral-300 px-3 py-2">
					<label
						v-for="category in categories"
						:key="category.id"
						class="flex items-center gap-2 text-sm text-neutral-700"
					>
						<input
							v-model="form.categories"
							type="checkbox"
							:value="category.id"
							class="h-4 w-4 rounded border-neutral-300 text-blue-600 focus:ring-blue-500"
						/>
						<span>{{ category.name }}</span>
					</label>
					<p v-if="categories.length === 0" class="text-sm text-neutral-500">No categories available.</p>
				</div>
			</div>

			<p v-if="error" class="text-sm text-red-600">{{ error }}</p>
			<p v-if="success" class="text-sm text-green-600">{{ success }}</p>

			<button
				type="submit"
				:disabled="isSubmitting"
				class="rounded-xl bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
			>
				{{ isSubmitting ? 'Creating...' : 'Create Product' }}
			</button>
		</form>
	</section>
</template>
