<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);
const user = computed(() => page.props.auth?.user as any);
const isRoot = computed(() => user.value?.role === 'root');
const isRestaurantAdmin = computed(() => user.value?.role === 'restaurant_admin');
const canManage = computed(() => isRestaurantAdmin.value);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
]);
</script>

<template>
    <Head :title="restaurant?.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ restaurant?.name }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ restaurant?.description }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="canManage"
                        :href="`/restaurants/${restaurant?.slug}/edit`"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Editar
                    </Link>
                    <Link
                        v-if="canManage"
                        :href="`/restaurants/${restaurant?.slug}/tables`"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Gerenciar Mesas
                    </Link>
                    <Link
                        :href="`/restaurants/${restaurant?.slug}/policies`"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Políticas de Reserva
                    </Link>
                    <Link
                        :href="`/restaurants/${restaurant?.slug}/reserve`"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        Fazer Reserva
                    </Link>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Informações -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Informações</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-muted-foreground mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <p class="font-medium">{{ restaurant?.address }}</p>
                                <p class="text-muted-foreground">{{ restaurant?.city }}, {{ restaurant?.state }} - {{ restaurant?.zip_code }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>{{ restaurant?.phone }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ restaurant?.email }}</span>
                        </div>

                        <div v-if="restaurant?.website" class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            <a :href="restaurant.website" target="_blank" class="text-primary hover:underline">
                                Visitar Website
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Reserva -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Políticas de Reserva</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Duração da reserva:</span>
                            <span class="font-medium">{{ restaurant?.reservation_duration }} minutos</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Intervalo entre horários:</span>
                            <span class="font-medium">{{ restaurant?.slot_interval }} minutos</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Antecedência mínima:</span>
                            <span class="font-medium">{{ restaurant?.min_advance_hours }} horas</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Antecedência máxima:</span>
                            <span class="font-medium">{{ restaurant?.max_advance_days }} dias</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Tamanho do grupo:</span>
                            <span class="font-medium">{{ restaurant?.min_party_size }} - {{ restaurant?.max_party_size }} pessoas</span>
                        </div>
                        <div class="flex items-center gap-2 pt-2">
                            <svg
                                v-if="restaurant?.auto_confirm_reservations"
                                class="w-5 h-5 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span :class="restaurant?.auto_confirm_reservations ? 'text-green-600 font-medium' : 'text-muted-foreground'">
                                {{ restaurant?.auto_confirm_reservations ? 'Confirmação automática' : 'Confirmação manual' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mesas -->
            <div class="rounded-lg border bg-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Mesas Disponíveis</h2>
                    <span class="text-sm text-muted-foreground">
                        {{ restaurant?.tables?.length || 0 }} mesas
                    </span>
                </div>

                <div v-if="restaurant?.tables && restaurant.tables.length > 0" class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="table in restaurant.tables"
                        :key="table.id"
                        class="rounded-lg border p-4 text-center"
                    >
                        <div class="text-2xl font-bold">{{ table.number }}</div>
                        <div class="text-sm text-muted-foreground mt-1">
                            {{ table.capacity }} {{ table.capacity === 1 ? 'pessoa' : 'pessoas' }}
                        </div>
                        <div class="mt-2">
                            <span class="inline-flex items-center rounded-full bg-primary/10 px-2 py-1 text-xs font-medium text-primary">
                                {{ table.type === 'internal' ? 'Interna' : table.type === 'external' ? 'Externa' : table.type === 'vip' ? 'VIP' : 'Janela' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 text-muted-foreground">
                    Nenhuma mesa cadastrada
                </div>
            </div>
        </div>
    </AppLayout>
</template>
