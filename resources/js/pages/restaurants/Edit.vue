<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
    { title: 'Editar', href: `/restaurants/${restaurant.value?.slug}/edit` },
]);

const form = useForm({
    name: restaurant.value?.name || '',
    slug: restaurant.value?.slug || '',
    description: restaurant.value?.description || '',
    address: restaurant.value?.address || '',
    city: restaurant.value?.city || '',
    state: restaurant.value?.state || '',
    zip_code: restaurant.value?.zip_code || '',
    phone: restaurant.value?.phone || '',
    email: restaurant.value?.email || '',
    website: restaurant.value?.website || '',
    auto_confirm_reservations: restaurant.value?.auto_confirm_reservations ?? false,
    slot_interval: restaurant.value?.slot_interval || 30,
    reservation_duration: restaurant.value?.reservation_duration || 120,
    min_advance_hours: restaurant.value?.min_advance_hours || 2,
    max_advance_days: restaurant.value?.max_advance_days || 30,
    min_party_size: restaurant.value?.min_party_size || 1,
    max_party_size: restaurant.value?.max_party_size || 10,
    cancellation_policy_enabled: restaurant.value?.cancellation_policy_enabled ?? true,
    cancellation_hours_before: restaurant.value?.cancellation_hours_before || 24,
    cancellation_policy_text: restaurant.value?.cancellation_policy_text || '',
});

function generateSlug() {
    form.slug = form.name
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
}

function submit() {
    form.put(`/restaurants/${restaurant.value.slug}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Editar ${restaurant?.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Editar Restaurante</h1>
                <p class="text-muted-foreground">{{ restaurant?.name }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Informações Básicas -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Informações Básicas</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <Label for="name">Nome do Restaurante *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                @blur="generateSlug"
                                required
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <Label for="slug">Slug (URL amigável) *</Label>
                            <Input id="slug" v-model="form.slug" required />
                            <InputError :message="form.errors.slug" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-1">
                                URL: /restaurants/{{ form.slug || 'slug-do-restaurante' }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <Label for="description">Descrição</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Endereço</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <Label for="address">Endereço *</Label>
                            <Input id="address" v-model="form.address" required />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>

                        <div>
                            <Label for="city">Cidade *</Label>
                            <Input id="city" v-model="form.city" required />
                            <InputError :message="form.errors.city" class="mt-2" />
                        </div>

                        <div>
                            <Label for="state">Estado (UF) *</Label>
                            <Input
                                id="state"
                                v-model="form.state"
                                maxlength="2"
                                placeholder="SP"
                                required
                            />
                            <InputError :message="form.errors.state" class="mt-2" />
                        </div>

                        <div>
                            <Label for="zip_code">CEP *</Label>
                            <Input
                                id="zip_code"
                                v-model="form.zip_code"
                                placeholder="00000-000"
                                required
                            />
                            <InputError :message="form.errors.zip_code" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Contato -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Contato</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label for="phone">Telefone *</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                placeholder="(11) 99999-9999"
                                required
                            />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <div>
                            <Label for="email">Email *</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <Label for="website">Website</Label>
                            <Input
                                id="website"
                                v-model="form.website"
                                type="url"
                                placeholder="https://..."
                            />
                            <InputError :message="form.errors.website" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Configurações de Reserva -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Configurações de Reserva</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <Label for="slot_interval">Intervalo entre horários (min) *</Label>
                            <Input
                                id="slot_interval"
                                v-model.number="form.slot_interval"
                                type="number"
                                min="15"
                                max="120"
                                required
                            />
                        </div>

                        <div>
                            <Label for="reservation_duration">Duração padrão (min) *</Label>
                            <Input
                                id="reservation_duration"
                                v-model.number="form.reservation_duration"
                                type="number"
                                min="30"
                                max="300"
                                required
                            />
                        </div>

                        <div>
                            <Label for="min_advance_hours">Antecedência mínima (horas) *</Label>
                            <Input
                                id="min_advance_hours"
                                v-model.number="form.min_advance_hours"
                                type="number"
                                min="0"
                                required
                            />
                        </div>

                        <div>
                            <Label for="max_advance_days">Antecedência máxima (dias) *</Label>
                            <Input
                                id="max_advance_days"
                                v-model.number="form.max_advance_days"
                                type="number"
                                min="1"
                                required
                            />
                        </div>

                        <div>
                            <Label for="min_party_size">Tamanho mín. grupo *</Label>
                            <Input
                                id="min_party_size"
                                v-model.number="form.min_party_size"
                                type="number"
                                min="1"
                                required
                            />
                        </div>

                        <div>
                            <Label for="max_party_size">Tamanho máx. grupo *</Label>
                            <Input
                                id="max_party_size"
                                v-model.number="form.max_party_size"
                                type="number"
                                min="1"
                                required
                            />
                        </div>

                        <div class="md:col-span-2 flex items-center gap-2">
                            <input
                                id="auto_confirm"
                                v-model="form.auto_confirm_reservations"
                                type="checkbox"
                                class="rounded"
                            />
                            <Label for="auto_confirm" class="!mb-0 cursor-pointer">
                                Confirmar reservas automaticamente
                            </Label>
                        </div>
                    </div>
                </div>

                <!-- Política de Cancelamento -->
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Política de Cancelamento</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <input
                                id="cancellation_enabled"
                                v-model="form.cancellation_policy_enabled"
                                type="checkbox"
                                class="rounded"
                            />
                            <Label for="cancellation_enabled" class="!mb-0 cursor-pointer">
                                Habilitar política de cancelamento
                            </Label>
                        </div>

                        <div v-if="form.cancellation_policy_enabled">
                            <Label for="cancellation_hours">Prazo mínimo para cancelamento (horas)</Label>
                            <Input
                                id="cancellation_hours"
                                v-model.number="form.cancellation_hours_before"
                                type="number"
                                min="1"
                                placeholder="24"
                            />
                            <p class="text-xs text-muted-foreground mt-1">
                                Cliente não poderá cancelar se faltar menos que isso para a reserva
                            </p>
                        </div>

                        <div v-if="form.cancellation_policy_enabled">
                            <Label for="cancellation_policy_text">Texto da Política (Opcional)</Label>
                            <Textarea
                                id="cancellation_policy_text"
                                v-model="form.cancellation_policy_text"
                                rows="3"
                                placeholder="Descreva detalhes adicionais sobre a política de cancelamento..."
                            />
                            <InputError :message="form.errors.cancellation_policy_text" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-3">
                    <Button
                        type="button"
                        variant="outline"
                        class="flex-1"
                        @click="$inertia.visit(`/restaurants/${restaurant.slug}`)"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        class="flex-1"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
