<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const reservation = computed(() => page.props.reservation as any);
const user = computed(() => page.props.auth?.user as any);
const isRoot = computed(() => user.value?.role === 'root');
const isRestaurantAdmin = computed(() => user.value?.role === 'restaurant_admin');

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Reservas', href: '/reservations' },
    { title: `Reserva #${reservation.value?.id}`, href: `/reservations/${reservation.value?.id}` },
]);

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
        'cancelled_by_customer': { text: 'Cancelada pelo Cliente', class: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' },
        'cancelled_by_restaurant': { text: 'Cancelada pelo Restaurante', class: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' },
        'completed': { text: 'Concluída', class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' },
        'no_show': { text: 'Não compareceu', class: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' },
    };
    return badges[status] || { text: status, class: 'bg-gray-100 text-gray-800' };
}

const canCancel = computed(() => {
    const res = reservation.value;
    if (!res) return false;

    // Verifica se status permite cancelamento
    if (!['pending', 'confirmed'].includes(res.status)) {
        return false;
    }

    // Root e restaurant admin sempre podem cancelar
    if (isRoot.value || isRestaurantAdmin.value) {
        return true;
    }

    // Cliente pode cancelar se for sua reserva
    if (res.user_id === user.value?.id) {
        return true;
    }

    return false;
});

const cancelForm = useForm({
    reason: '',
});

function handleCancel() {
    if (!confirm('Tem certeza que deseja cancelar esta reserva?')) {
        return;
    }

    cancelForm.post(`/reservations/${reservation.value.id}/cancel`, {
        preserveScroll: true,
        onSuccess: () => {
            cancelForm.reset();
        },
    });
}

const statusForm = useForm({
    status: reservation.value?.status || 'pending',
});

function updateStatus() {
    statusForm.post(`/reservations/${reservation.value.id}/status`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Reserva #${reservation?.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Reserva #{{ reservation?.id }}
                    </h1>
                    <p class="text-muted-foreground">
                        Detalhes da reserva
                    </p>
                </div>
                <span
                    class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium"
                    :class="getStatusBadge(reservation?.status).class"
                >
                    {{ getStatusBadge(reservation?.status).text }}
                </span>
            </div>

            <!-- Informações do Restaurante -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-lg font-semibold mb-4">Restaurante</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-muted-foreground">Nome</p>
                        <Link
                            :href="`/restaurants/${reservation?.restaurant?.slug}`"
                            class="text-lg font-medium hover:underline"
                        >
                            {{ reservation?.restaurant?.name }}
                        </Link>
                    </div>
                    <div v-if="reservation?.restaurant?.address">
                        <p class="text-sm text-muted-foreground">Endereço</p>
                        <p class="text-sm">{{ reservation?.restaurant?.address }}</p>
                        <p class="text-sm">{{ reservation?.restaurant?.city }} - {{ reservation?.restaurant?.state }}</p>
                    </div>
                    <div v-if="reservation?.restaurant?.phone">
                        <p class="text-sm text-muted-foreground">Telefone</p>
                        <p class="text-sm">{{ reservation?.restaurant?.phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Informações da Reserva -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-lg font-semibold mb-4">Detalhes da Reserva</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-muted-foreground">Data e Hora</p>
                        <p class="text-base font-medium">{{ formatDateTime(reservation?.reservation_datetime) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Número de Pessoas</p>
                        <p class="text-base font-medium">{{ reservation?.party_size }} pessoas</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Mesa</p>
                        <p class="text-base font-medium">{{ reservation?.table?.number || 'A definir' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Duração</p>
                        <p class="text-base font-medium">{{ reservation?.duration }} minutos</p>
                    </div>
                </div>
                <div v-if="reservation?.notes" class="mt-4">
                    <p class="text-sm text-muted-foreground">Observações</p>
                    <p class="text-sm mt-1">{{ reservation?.notes }}</p>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-lg font-semibold mb-4">Cliente</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-muted-foreground">Nome</p>
                        <p class="text-base font-medium">{{ reservation?.user?.name || reservation?.guest_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Email</p>
                        <p class="text-base">{{ reservation?.user?.email || reservation?.guest_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Telefone</p>
                        <p class="text-base">{{ reservation?.user?.phone || reservation?.guest_phone }}</p>
                    </div>
                </div>
            </div>

            <!-- Informações de Cancelamento -->
            <div v-if="reservation?.cancelled_at" class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/10 dark:border-red-900/30 p-6">
                <h2 class="text-lg font-semibold mb-4 text-red-900 dark:text-red-400">Cancelamento</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-muted-foreground">Data do Cancelamento</p>
                        <p class="text-base font-medium">{{ formatDateTime(reservation?.cancelled_at) }}</p>
                    </div>
                    <div v-if="reservation?.cancellation_reason">
                        <p class="text-sm text-muted-foreground">Motivo</p>
                        <p class="text-sm">{{ reservation?.cancellation_reason }}</p>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex flex-wrap gap-3">
                <Link
                    href="/reservations"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground"
                >
                    Voltar
                </Link>

                <!-- Cancelar Reserva -->
                <Button
                    v-if="canCancel"
                    variant="destructive"
                    @click="handleCancel"
                    :disabled="cancelForm.processing"
                >
                    {{ cancelForm.processing ? 'Cancelando...' : 'Cancelar Reserva' }}
                </Button>

                <!-- Alterar Status (Root/Admin) -->
                <div v-if="isRoot || isRestaurantAdmin" class="flex items-center gap-2">
                    <select
                        v-model="statusForm.status"
                        class="rounded-md border border-input bg-background px-3 py-2 text-sm"
                    >
                        <option value="pending">Pendente</option>
                        <option value="confirmed">Confirmada</option>
                        <option value="cancelled_by_restaurant">Cancelada pelo Restaurante</option>
                        <option value="completed">Concluída</option>
                        <option value="no_show">Não compareceu</option>
                    </select>
                    <Button
                        @click="updateStatus"
                        :disabled="statusForm.processing || statusForm.status === reservation?.status"
                    >
                        {{ statusForm.processing ? 'Atualizando...' : 'Atualizar Status' }}
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
