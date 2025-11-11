<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reservas', href: '/reservations' },
];

const page = usePage();
const reservations = computed(() => page.props.reservations as any);
const user = computed(() => page.props.auth?.user as any);
const isRoot = computed(() => user.value?.role === 'root');

function formatDateTime(datetime: string) {
    const date = new Date(datetime);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getStatusBadge(status: string) {
    const badges: Record<string, { text: string; class: string }> = {
        'pending': { text: 'Pendente', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' },
        'confirmed': { text: 'Confirmada', class: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' },
        'cancelled_by_customer': { text: 'Cancelada', class: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' },
        'cancelled_by_restaurant': { text: 'Cancelada', class: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' },
        'completed': { text: 'Concluída', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' },
        'no_show': { text: 'Não compareceu', class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' },
    };
    return badges[status] || { text: status, class: 'bg-gray-100 text-gray-800' };
}
</script>

<template>
    <Head title="Reservas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ isRoot ? 'Todas as Reservas' : 'Minhas Reservas' }}
                    </h1>
                    <p class="text-muted-foreground">
                        Gerencie suas reservas
                    </p>
                </div>
            </div>

            <div v-if="reservations?.data && reservations.data.length > 0" class="rounded-lg border">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium">Restaurante</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Data/Hora</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Mesa</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Pessoas</th>
                                <th v-if="isRoot" class="px-4 py-3 text-left text-sm font-medium">Cliente</th>
                                <th class="px-4 py-3 text-left text-sm font-medium">Status</th>
                                <th class="px-4 py-3 text-right text-sm font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="reservation in reservations.data"
                                :key="reservation.id"
                                class="hover:bg-muted/50 transition-colors"
                            >
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/restaurants/${reservation.restaurant?.slug}`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ reservation.restaurant?.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ formatDateTime(reservation.reservation_datetime) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ reservation.table?.number || '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ reservation.party_size }}
                                </td>
                                <td v-if="isRoot" class="px-4 py-3 text-sm">
                                    {{ reservation.user?.name || reservation.guest_name }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="getStatusBadge(reservation.status).class"
                                    >
                                        {{ getStatusBadge(reservation.status).text }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="`/reservations/${reservation.id}`"
                                        class="text-primary hover:underline text-sm font-medium"
                                    >
                                        Ver Detalhes
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div v-if="reservations.links && reservations.links.length > 3" class="border-t px-4 py-3 flex items-center justify-between">
                    <div class="text-sm text-muted-foreground">
                        Mostrando {{ reservations.from }} até {{ reservations.to }} de {{ reservations.total }} reservas
                    </div>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, index) in reservations.links"
                            :key="index"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1 text-sm rounded',
                                link.active
                                    ? 'bg-primary text-primary-foreground'
                                    : link.url
                                        ? 'hover:bg-muted'
                                        : 'opacity-50 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <svg class="mx-auto h-12 w-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold">Nenhuma reserva encontrada</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Faça sua primeira reserva em um de nossos restaurantes!
                </p>
                <Link
                    href="/restaurants"
                    class="mt-4 inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Ver Restaurantes
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
