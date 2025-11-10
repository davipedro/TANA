<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);
const tables = computed(() => page.props.tables as any[]);
const user = computed(() => page.props.auth?.user as any);
const canManage = computed(() => {
    if (!user.value) return false;
    return user.value.role === 'root' || user.value.role === 'restaurant_admin';
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
    { title: 'Mesas', href: `/restaurants/${restaurant.value?.slug}/tables` },
]);

function getTypeLabel(type: string) {
    const types: Record<string, string> = {
        'internal': 'Interna',
        'external': 'Externa',
        'window': 'Janela',
        'vip': 'VIP',
    };
    return types[type] || type;
}

function deleteTable(tableId: number) {
    if (!confirm('Tem certeza que deseja excluir esta mesa?')) {
        return;
    }

    router.delete(`/restaurants/${restaurant.value.slug}/tables/${tableId}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Mesas - ${restaurant?.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Mesas - {{ restaurant?.name }}
                    </h1>
                    <p class="text-muted-foreground">
                        Gerencie as mesas do restaurante
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="`/restaurants/${restaurant?.slug}`"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Voltar
                    </Link>
                    <Link
                        v-if="canManage"
                        :href="`/restaurants/${restaurant?.slug}/tables/create`"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                    >
                        Nova Mesa
                    </Link>
                </div>
            </div>

            <div v-if="tables && tables.length > 0" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="table in tables"
                    :key="table.id"
                    class="rounded-lg border bg-card p-6 flex flex-col"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-3xl font-bold">{{ table.number }}</div>
                            <div class="text-sm text-muted-foreground mt-1">
                                Mesa {{ table.number }}
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                            :class="table.is_active
                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                            "
                        >
                            {{ table.is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>

                    <div class="space-y-2 text-sm flex-1">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Capacidade:</span>
                            <span class="font-medium">{{ table.capacity }} pessoas</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Tipo:</span>
                            <span class="font-medium">{{ getTypeLabel(table.type) }}</span>
                        </div>
                        <div v-if="table.reservations_count !== undefined" class="flex justify-between">
                            <span class="text-muted-foreground">Reservas:</span>
                            <span class="font-medium">{{ table.reservations_count }}</span>
                        </div>
                    </div>

                    <div v-if="canManage" class="mt-4 flex gap-2">
                        <Link
                            :href="`/restaurants/${restaurant?.slug}/tables/${table.id}/edit`"
                            class="flex-1 inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-sm font-medium hover:bg-accent"
                        >
                            Editar
                        </Link>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="deleteTable(table.id)"
                        >
                            Excluir
                        </Button>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <svg class="mx-auto h-12 w-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold">Nenhuma mesa cadastrada</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Comece adicionando mesas ao restaurante.
                </p>
                <Link
                    v-if="canManage"
                    :href="`/restaurants/${restaurant?.slug}/tables/create`"
                    class="mt-4 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Adicionar Mesa
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
