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
        <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-2xl mx-auto">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Fazer Reserva
                </h1>
                <p class="text-muted-foreground">
                    {{ restaurant?.name }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Data e Hora -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Data e Hora</h2>
                    <div class="space-y-4">
                        <div>
                            <Label for="reservation_datetime">Data e Hora da Reserva *</Label>
                            <Input
                                id="reservation_datetime"
                                v-model="form.reservation_datetime"
                                type="datetime-local"
                                :min="getMinDateTime()"
                                :max="getMaxDateTime()"
                                required
                            />
                            <InputError :message="form.errors.reservation_datetime" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-1">
                                Antecedência: {{ restaurant?.min_advance_hours }}h - {{ restaurant?.max_advance_days }} dias
                            </p>
                        </div>

                        <div>
                            <Label for="party_size">Número de Pessoas *</Label>
                            <Input
                                id="party_size"
                                v-model.number="form.party_size"
                                type="number"
                                :min="restaurant?.min_party_size || 1"
                                :max="restaurant?.max_party_size || 10"
                                required
                            />
                            <InputError :message="form.errors.party_size" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Mesa (Opcional) -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Escolha uma Mesa (Opcional)</h2>
                    <div v-if="restaurant?.tables && restaurant.tables.length > 0" class="grid grid-cols-3 gap-3">
                        <label
                            v-for="table in restaurant.tables"
                            :key="table.id"
                            class="relative flex flex-col items-center justify-center rounded-lg border p-4 cursor-pointer hover:bg-accent transition-colors"
                            :class="{ 'border-primary bg-primary/5': form.table_id === table.id }"
                        >
                            <input
                                type="radio"
                                v-model="form.table_id"
                                :value="table.id"
                                class="sr-only"
                            />
                            <div class="text-xl font-bold">{{ table.number }}</div>
                            <div class="text-xs text-muted-foreground">{{ table.capacity }} pessoas</div>
                            <div class="text-xs text-muted-foreground">{{ table.type }}</div>
                        </label>
                    </div>
                    <p class="text-xs text-muted-foreground mt-2">
                        Deixe em branco para que o restaurante escolha a melhor mesa
                    </p>
                </div>

                <!-- Dados do Cliente (se não autenticado) -->
                <div v-if="!isAuthenticated" class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Seus Dados</h2>
                    <div class="space-y-4">
                        <div>
                            <Label for="guest_name">Nome Completo *</Label>
                            <Input
                                id="guest_name"
                                v-model="form.guest_name"
                                type="text"
                                placeholder="João Silva"
                                required
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
                            />
                            <InputError :message="form.errors.guest_phone" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Observações</h2>
                    <div>
                        <Label for="notes">Observações (Opcional)</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            placeholder="Ex: Aniversário, preferência por mesa perto da janela..."
                            rows="3"
                        />
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>
                </div>

                <!-- Resumo -->
                <div class="rounded-lg border bg-primary/5 border-primary/20 p-6">
                    <h3 class="font-semibold mb-2">Resumo da Reserva</h3>
                    <ul class="text-sm space-y-1 text-muted-foreground">
                        <li>Duração: {{ restaurant?.reservation_duration }} minutos</li>
                        <li v-if="restaurant?.auto_confirm_reservations" class="text-green-600 font-medium">
                            ✓ Confirmação automática
                        </li>
                        <li v-else class="text-yellow-600">
                            ⏳ Aguarda confirmação do restaurante
                        </li>
                    </ul>
                </div>

                <!-- Botões -->
                <div class="flex gap-3">
                    <Button
                        type="button"
                        variant="outline"
                        class="flex-1"
                        @click="$inertia.visit(`/restaurants/${restaurant?.slug}`)"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        class="flex-1"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Criando...' : 'Confirmar Reserva' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
