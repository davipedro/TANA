<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Restaurantes', href: '/restaurants' },
];

const page = usePage();
const restaurants = computed(() => page.props.restaurants as any[]);
const user = computed(() => page.props.auth?.user as any);
const isRoot = computed(() => user.value?.role === 'root');
</script>

<template>
    <Head title="Restaurantes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Restaurantes</h1>
                    <p class="text-muted-foreground">
                        Explore nossos restaurantes parceiros
                    </p>
                </div>
                <Link
                    v-if="isRoot"
                    href="/restaurants/create"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Novo Restaurante
                </Link>
            </div>

            <div
                v-if="restaurants && restaurants.length > 0"
                class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="restaurant in restaurants"
                    :key="restaurant.id"
                    class="group relative overflow-hidden rounded-lg border bg-card transition-all hover:shadow-lg flex flex-col"
                >
                    <div class="p-6 flex flex-col flex-1">
                        <div class="mb-3">
                            <h3 class="text-xl font-semibold line-clamp-1">
                                {{ restaurant.name }}
                            </h3>
                            <p class="text-sm text-muted-foreground mt-1 line-clamp-2">
                                {{ restaurant.description }}
                            </p>
                        </div>

                        <div class="space-y-2 text-sm text-muted-foreground flex-1">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ restaurant.city }}, {{ restaurant.state }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>{{ restaurant.tables_count }} mesas</span>
                            </div>
                            <div v-if="restaurant.auto_confirm_reservations" class="flex items-center gap-2 text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Confirmação automática</span>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <Link
                                :href="`/restaurants/${restaurant.slug}`"
                                class="flex-1 inline-flex items-center justify-center rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                            >
                                Ver Detalhes
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center animate-in fade-in-50"
            >
                <svg class="mx-auto h-12 w-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold">Nenhum restaurante encontrado</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ isRoot ? 'Comece criando seu primeiro restaurante.' : 'Aguarde novos restaurantes parceiros!' }}
                </p>
                <Link
                    v-if="isRoot"
                    href="/restaurants/create"
                    class="mt-4 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Criar Restaurante
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
