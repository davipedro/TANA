<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const restaurant = computed(() => page.props.restaurant as any);
const user = computed(() => page.props.auth?.user as any);
const canEdit = computed(() => {
    if (!user.value) return false;
    return user.value.role === 'restaurant_admin';
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Restaurantes', href: '/restaurants' },
    { title: restaurant.value?.name || '', href: `/restaurants/${restaurant.value?.slug}` },
    { title: 'Políticas de Reserva', href: `/restaurants/${restaurant.value?.slug}/policies` },
]);
</script>

<template>
    <Head :title="`Políticas de Reserva - ${restaurant?.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Informações e Políticas de Reserva
                </h1>
                <p class="text-muted-foreground mt-1">
                    {{ restaurant?.name }}
                </p>
            </div>

            <!-- Horários e Configurações -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-4">Configurações de Reserva</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-muted-foreground">Duração da reserva</span>
                            <span class="text-lg font-medium mt-1">{{ restaurant?.reservation_duration }} minutos</span>
                            <span class="text-xs text-muted-foreground mt-1">Tempo padrão de permanência na mesa</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-muted-foreground">Intervalo entre horários</span>
                            <span class="text-lg font-medium mt-1">{{ restaurant?.slot_interval }} minutos</span>
                            <span class="text-xs text-muted-foreground mt-1">Espaçamento entre reservas</span>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <h3 class="font-semibold mb-3">Antecedência</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-muted-foreground">Antecedência mínima</span>
                                <span class="text-lg font-medium mt-1">{{ restaurant?.min_advance_hours }} horas</span>
                                <span class="text-xs text-muted-foreground mt-1">Você deve reservar com pelo menos essa antecedência</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-muted-foreground">Antecedência máxima</span>
                                <span class="text-lg font-medium mt-1">{{ restaurant?.max_advance_days }} dias</span>
                                <span class="text-xs text-muted-foreground mt-1">Você pode reservar com até essa antecedência</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <h3 class="font-semibold mb-3">Tamanho do Grupo</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm text-muted-foreground">Mínimo de pessoas</span>
                                <span class="text-lg font-medium mt-1">{{ restaurant?.min_party_size }} {{ restaurant?.min_party_size === 1 ? 'pessoa' : 'pessoas' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm text-muted-foreground">Máximo de pessoas</span>
                                <span class="text-lg font-medium mt-1">{{ restaurant?.max_party_size }} pessoas</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-4">
                        <h3 class="font-semibold mb-3">Confirmação</h3>
                        <div class="flex items-start gap-3 p-4 rounded-lg" :class="restaurant?.auto_confirm_reservations ? 'bg-green-50 dark:bg-green-950/20' : 'bg-yellow-50 dark:bg-yellow-950/20'">
                            <svg
                                class="w-6 h-6 mt-0.5"
                                :class="restaurant?.auto_confirm_reservations ? 'text-green-600' : 'text-yellow-600'"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path v-if="restaurant?.auto_confirm_reservations" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-medium" :class="restaurant?.auto_confirm_reservations ? 'text-green-900 dark:text-green-400' : 'text-yellow-900 dark:text-yellow-400'">
                                    {{ restaurant?.auto_confirm_reservations ? 'Confirmação Automática' : 'Confirmação Manual' }}
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    {{ restaurant?.auto_confirm_reservations
                                        ? 'Sua reserva será confirmada automaticamente após o envio.'
                                        : 'Sua reserva ficará pendente até que o restaurante confirme.'
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Política de Cancelamento -->
            <div v-if="restaurant?.cancellation_policy_enabled" class="rounded-lg border border-orange-200 bg-orange-50 dark:bg-orange-950/20 dark:border-orange-900/30 p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-orange-900 dark:text-orange-400 mb-2">
                            Política de Cancelamento
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <p class="font-medium text-orange-900 dark:text-orange-400">
                                    Cancelamento com antecedência de {{ restaurant?.cancellation_hours_before }} horas
                                </p>
                                <p class="text-sm text-muted-foreground mt-1">
                                    Você pode cancelar sua reserva gratuitamente até {{ restaurant?.cancellation_hours_before }} horas antes do horário agendado.
                                </p>
                            </div>
                            <div v-if="restaurant?.cancellation_policy_text" class="border-t border-orange-200 dark:border-orange-900/30 pt-3">
                                <p class="text-sm whitespace-pre-line">{{ restaurant?.cancellation_policy_text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Política Flexível -->
            <div v-else class="rounded-lg border border-green-200 bg-green-50 dark:bg-green-950/20 dark:border-green-900/30 p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h2 class="text-xl font-semibold text-green-900 dark:text-green-400 mb-2">
                            Política de Cancelamento Flexível
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Este restaurante permite cancelamento a qualquer momento, sem restrições de horário.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contato -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-4">Informações de Contato</h2>
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
                        <a :href="`tel:${restaurant?.phone}`" class="hover:underline">{{ restaurant?.phone }}</a>
                    </div>

                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a :href="`mailto:${restaurant?.email}`" class="hover:underline">{{ restaurant?.email }}</a>
                    </div>

                    <div v-if="restaurant?.website" class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <a :href="restaurant?.website" target="_blank" class="text-primary hover:underline">
                            Visitar Website
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex gap-3">
                <Link
                    :href="`/restaurants/${restaurant?.slug}`"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                >
                    Voltar
                </Link>
                <Link
                    v-if="canEdit"
                    :href="`/restaurants/${restaurant?.slug}/edit`"
                    class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-accent"
                >
                    Editar Políticas
                </Link>
                <Link
                    :href="`/restaurants/${restaurant?.slug}/reserve`"
                    class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                >
                    Fazer Reserva
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
