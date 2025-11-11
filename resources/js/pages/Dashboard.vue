<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage();
const stats = computed(() => page.props.stats as any);
const user = computed(() => page.props.auth?.user as any);
const isRoot = computed(() => user.value?.role === 'root');
const isRestaurantAdmin = computed(() => user.value?.role === 'restaurant_admin');
const isCustomer = computed(() => user.value?.role === 'customer');
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Bem-vindo, {{ user?.name }}!
                </h1>
                <p class="text-muted-foreground">
                    {{ isRoot ? 'Painel Administrativo' : isRestaurantAdmin ? 'Painel do Restaurante' : 'Suas Reservas' }}
                </p>
            </div>

            <!-- Stats Cards - Root -->
            <div v-if="isRoot" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Restaurantes
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.restaurants || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Mesas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.tables || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Total de Reservas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.reservations || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Pendentes
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ stats?.pending_reservations || 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards - Restaurant Admin -->
            <div v-else-if="isRestaurantAdmin" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Meus Restaurantes
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.restaurants || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Mesas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.tables || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Total de Reservas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.reservations || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Pendentes
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ stats?.pending_reservations || 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards - Customer -->
            <div v-else class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Minhas Reservas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ stats?.reservations || 0 }}</div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <p class="text-sm font-medium text-muted-foreground">
                            Próximas Reservas
                        </p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ stats?.upcoming_reservations || 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-4">Ações Rápidas</h2>
                <div class="flex flex-wrap gap-4">
                    <Link
                        href="/restaurants"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Ver Restaurantes
                    </Link>
                    <Link
                        v-if="isRoot"
                        href="/restaurants/create"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        Novo Restaurante
                    </Link>
                    <Link
                        href="/reservations"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                    >
                        {{ isRoot ? 'Todas as Reservas' : isRestaurantAdmin ? 'Todas as Reservas' : 'Minhas Reservas' }}
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
