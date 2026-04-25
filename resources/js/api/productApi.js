function buildErrorMessage(errorData, fallbackMessage) {
    if (errorData?.message) {
        return errorData.message;
    }

    if (errorData?.error) {
        return errorData.error;
    }

    if (errorData?.errors && typeof errorData.errors === 'object') {
        const firstError = Object.values(errorData.errors)[0];
        if (Array.isArray(firstError) && firstError.length > 0) {
            return firstError[0];
        }
    }

    return fallbackMessage;
}

export async function createProduct(data) {
    const formData = new FormData();
    formData.append('name', data.name);
    formData.append('price', data.price);
    formData.append('description', data.description || '');
    formData.append('image', data.image);

    if (Array.isArray(data.categories) && data.categories.length > 0) {
        formData.append('categories', JSON.stringify(data.categories));
    }

    const response = await fetch('/api/products', {
        method: 'POST',
        body: formData,
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => null);
        throw new Error(buildErrorMessage(errorData, 'Failed to create product'));
    }

    return response.json();
}

export async function getProducts(params = {}) {
    const searchParams = new URLSearchParams();

    if (params.sortName) {
        searchParams.set('sort_name', params.sortName);
    }

    if (params.sortPrice) {
        searchParams.set('sort_price', params.sortPrice);
    }

    if (params.category) {
        searchParams.set('category', params.category);
    }

    if (params.page) {
        searchParams.set('page', params.page);
    }

    if (params.limit) {
        searchParams.set('limit', params.limit);
    }

    const query = searchParams.toString();
    const url = query ? `/api/products?${query}` : '/api/products';

    const response = await fetch(url);

    if (!response.ok) {
        const errorData = await response.json().catch(() => null);
        throw new Error(buildErrorMessage(errorData, 'Failed to fetch products'));
    }

    return response.json();
}

export async function getCategories() {
    const response = await fetch('/api/categories');

    if (!response.ok) {
        const errorData = await response.json().catch(() => null);
        throw new Error(buildErrorMessage(errorData, 'Failed to fetch categories'));
    }

    return response.json();
}