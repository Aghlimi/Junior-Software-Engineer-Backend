<script setup>
import { ref } from 'vue';
import CreateProductPage from './pages/create.vue';
import ProductListPage from './pages/list.vue';

const listReloadToken = ref(0);

function onProductCreated() {
    listReloadToken.value += 1;
}
</script>

<template>
    <main class="min-h-screen bg-slate-50">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 lg:grid-cols-[1fr_2fr]">
            <CreateProductPage @created="onProductCreated" />
            <ProductListPage :reload-token="listReloadToken" />
        </div>
    </main>
</template>