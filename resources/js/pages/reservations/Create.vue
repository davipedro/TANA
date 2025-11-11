<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);
const user = computed(() => page.props.auth?.user as any);
const isAuthenticated = computed(() => !!user.value);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
    { title: 'Nova Reserva', href: `/restaurants/${restaurant.value?.slug}/reserve` },
]);

const form = useForm({
    restaurant_id: restaurant.value?.id,
    table_id: null as number | null,
    guest_name: '',
    guest_email: '',
    guest_phone: '',
    reservation_datetime: '',
    party_size: 2,
    notes: '',
});

function submit() {
    form.post('/reservations', {
        preserveScroll: true,
    });
}

// Função para formatar datetime-local
function getMinDateTime() {
    const now = new Date();
    now.setMinutes(now.getMinutes() + (restaurant.value?.min_advance_hours * 60 || 120));
    return now.toISOString().slice(0, 16);
}

function getMaxDateTime() {
    const max = new Date();
    max.setDate(max.getDate() + (restaurant.value?.max_advance_days || 30));
    return max.toISOString().slice(0, 16);
}
</script>

<template>
    <Head title="Nova Reserva" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h1 class="text-4xl font-bold tracking-tight">
                    Fazer Reserva
                </h1>
                <p class="text-lg text-muted-foreground">
                    {{ restaurant?.name }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Data, Hora e Pessoas - Grid 2 colunas -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Data e Hora -->
                    <div class="rounded-xl border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h2 class="text-lg font-semibold">Data e Hora</h2>
                        </div>
                        <div>
                            <Label for="reservation_datetime">Quando? *</Label>
                            <Input
                                id="reservation_datetime"
                                v-model="form.reservation_datetime"
                                type="datetime-local"
                                :min="getMinDateTime()"
                                :max="getMaxDateTime()"
                                required
                                class="mt-2"
                            />
                            <InputError :message="form.errors.reservation_datetime" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-2">
                                Antecedência: {{ restaurant?.min_advance_hours }}h até {{ restaurant?.max_advance_days }} dias
                            </p>
                        </div>
                    </div>

                    <!-- Número de Pessoas -->
                    <div class="rounded-xl border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold">Tamanho do Grupo</h2>
                        </div>
                        <div>
                            <Label for="party_size">Quantas pessoas? *</Label>
                            <Input
                                id="party_size"
                                v-model.number="form.party_size"
                                type="number"
                                :min="restaurant?.min_party_size || 1"
                                :max="restaurant?.max_party_size || 10"
                                required
                                class="mt-2"
                            />
                            <InputError :message="form.errors.party_size" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-2">
                                Entre {{ restaurant?.min_party_size }} e {{ restaurant?.max_party_size }} pessoas
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mesa (Opcional) -->
                <div class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <h2 class="text-lg font-semibold">Escolha uma Mesa</h2>
                        <span class="text-sm text-muted-foreground ml-auto">(Opcional)</span>
                    </div>

                    <div v-if="restaurant?.tables && restaurant.tables.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-6">
                        <div
                            v-for="table in restaurant.tables"
                            :key="table.id"
                            class="group relative flex flex-col items-center justify-center rounded-xl border-2 p-6 cursor-pointer transition-all hover:scale-105 hover:shadow-md"
                            :class="form.table_id === table.id
                                ? 'border-primary bg-primary/10 shadow-lg ring-2 ring-primary/20'
                                : 'border-border bg-card hover:border-primary/50'"
                            @click="form.table_id === table.id ? form.table_id = null : form.table_id = table.id"
                        >

                            <!-- Checkmark quando selecionado -->
                            <div v-if="form.table_id === table.id" class="absolute top-2 right-2 w-5 h-5 bg-primary rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-primary-foreground" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div class="text-3xl font-bold mb-2" :class="form.table_id === table.id ? 'text-primary' : ''">
                                {{ table.number }}
                            </div>
                            <div class="text-sm font-medium text-muted-foreground mb-1">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ table.capacity }} pessoas
                            </div>
                            <div class="text-xs px-2 py-1 rounded-full bg-muted text-muted-foreground">
                                {{ table.type === 'internal' ? 'Interna' : table.type === 'external' ? 'Externa' : table.type === 'window' ? 'Janela' : 'VIP' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-lg bg-muted/50">
                        <p class="text-sm text-muted-foreground text-center">
                            💡 Deixe em branco para que o restaurante escolha a melhor mesa automaticamente
                        </p>
                    </div>
                </div>

                <!-- Dados do Cliente (se não autenticado) -->
                <div v-if="!isAuthenticated" class="rounded-xl border bg-card p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-6">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h2 class="text-lg font-semibold">Seus Dados</h2>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <Label for="guest_name">Nome Completo *</Label>
                            <Input
                                id="guest_name"
                                v-model="form.guest_name"
                                type="text"
                                placeholder="João Silva"
                                required
                                class="mt-2"
                            />
                            <InputError :message="form.errors.guest_name" class="mt-2" />
                        </div>

                        <div>
                            <Label for="guest_email">Email *</Label>
                            <Input
                                id="guest_email"
                                v-model="form.guest_email"
                                type="email"
                                placeholder="joao@example.com"
                                required
                                class="mt-2"
                            />
                            <InputError :message="form.errors.guest_email" class="mt-2" />
                        </div>

                        <div>
                            <Label for="guest_phone">Telefone *</Label>
                            <Input
                                id="guest_phone"
                                v-model="form.guest_phone"
                                type="tel"
                                placeholder="(11) 99999-9999"
                                required
                                class="mt-2"
                            />
                            <InputError :message="form.errors.guest_phone" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Observações e Resumo - Grid 2 colunas -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Observações -->
                    <div class="rounded-xl border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <h2 class="text-lg font-semibold">Observações</h2>
                            <span class="text-sm text-muted-foreground ml-auto">(Opcional)</span>
                        </div>
                        <div>
                            <Label for="notes">Alguma preferência ou ocasião especial?</Label>
                            <Textarea
                                id="notes"
                                v-model="form.notes"
                                placeholder="Ex: Aniversário, preferência por mesa perto da janela, restrições alimentares..."
                                rows="4"
                                class="mt-2"
                            />
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="rounded-xl border-2 bg-gradient-to-br from-primary/5 to-primary/10 border-primary/30 p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="text-lg font-semibold">Resumo</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-background/50">
                                <svg class="w-5 h-5 text-muted-foreground mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium">Duração da Reserva</p>
                                    <p class="text-sm text-muted-foreground">{{ restaurant?.reservation_duration }} minutos</p>
                                </div>
                            </div>

                            <div v-if="restaurant?.auto_confirm_reservations" class="flex items-start gap-3 p-3 rounded-lg bg-green-500/10 border border-green-500/20">
                                <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-green-700 dark:text-green-500">Confirmação Automática</p>
                                    <p class="text-xs text-green-600 dark:text-green-500/80">Sua reserva será confirmada imediatamente</p>
                                </div>
                            </div>

                            <div v-else class="flex items-start gap-3 p-3 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-700 dark:text-yellow-500">Aguarda Confirmação</p>
                                    <p class="text-xs text-yellow-600 dark:text-yellow-500/80">O restaurante confirmará sua reserva em breve</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-4 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        size="lg"
                        class="flex-1"
                        @click="$inertia.visit(`/restaurants/${restaurant?.slug}`)"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar
                    </Button>
                    <Button
                        type="submit"
                        size="lg"
                        class="flex-1 shadow-lg"
                        :disabled="form.processing"
                    >
                        <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ form.processing ? 'Processando...' : 'Confirmar Reserva' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
