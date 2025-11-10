<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);
const table = computed(() => page.props.table as any);

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
    { title: 'Mesas', href: `/restaurants/${restaurant.value?.slug}/tables` },
    { title: `Editar Mesa ${table.value?.number}`, href: `/restaurants/${restaurant.value?.slug}/tables/${table.value?.id}/edit` },
]);

const form = useForm({
    number: table.value?.number || '',
    capacity: table.value?.capacity || 4,
    type: table.value?.type || 'internal',
    is_active: table.value?.is_active ?? true,
});

function submit() {
    form.put(`/restaurants/${restaurant.value.slug}/tables/${table.value.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Editar Mesa ${table?.number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-2xl mx-auto">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Editar Mesa {{ table?.number }}
                </h1>
                <p class="text-muted-foreground">
                    {{ restaurant?.name }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-lg font-semibold mb-4">Informações da Mesa</h2>
                    <div class="space-y-4">
                        <div>
                            <Label for="number">Número da Mesa *</Label>
                            <Input
                                id="number"
                                v-model="form.number"
                                type="text"
                                placeholder="Ex: 1, A1, VIP-01"
                                required
                            />
                            <InputError :message="form.errors.number" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-1">
                                Identificador único da mesa
                            </p>
                        </div>

                        <div>
                            <Label for="capacity">Capacidade *</Label>
                            <Input
                                id="capacity"
                                v-model.number="form.capacity"
                                type="number"
                                min="1"
                                max="20"
                                required
                            />
                            <InputError :message="form.errors.capacity" class="mt-2" />
                            <p class="text-xs text-muted-foreground mt-1">
                                Número de pessoas que a mesa comporta
                            </p>
                        </div>

                        <div>
                            <Label for="type">Tipo *</Label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                                <option value="internal">Interna</option>
                                <option value="external">Externa</option>
                                <option value="window">Janela</option>
                                <option value="vip">VIP</option>
                            </select>
                            <InputError :message="form.errors.type" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            />
                            <Label for="is_active" class="cursor-pointer">
                                Mesa ativa (disponível para reservas)
                            </Label>
                        </div>
                        <InputError :message="form.errors.is_active" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="`/restaurants/${restaurant?.slug}/tables`"
                        class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                    >
                        Cancelar
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
