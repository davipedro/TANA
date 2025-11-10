<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const { appearance, updateAppearance } = useAppearance();

function toggleTheme() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <Head title="Bem-vindo ao TANA" />

    <!--
      Container principal com fundo gradiente e overflow-hidden para conter os efeitos de 'glow'.
      O gradiente vai de um cinza escuro para um tom quase preto, criando profundidade.
    -->
    <div class="relative flex min-h-screen flex-col overflow-hidden bg-background text-foreground">

        <!-- Topbar -->
        <header class="relative z-20 border-b border-border/40 bg-background/80 backdrop-blur-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 transition-opacity hover:opacity-80">
                    <img
                        src="/transparent-logo.svg"
                        alt="Logo Tana"
                        class="h-8 w-auto"
                    />
                    <span class="text-xl font-bold">TANA</span>
                </Link>

                <!-- Navigation Links -->
                <nav class="flex items-center gap-2">
                    <Link
                        href="/login"
                        class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground hover:bg-accent"
                    >
                        Entrar
                    </Link>
                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                    >
                        Criar Conta
                    </Link>

                    <!-- Theme Toggle -->
                    <button
                        @click="toggleTheme"
                        class="rounded-md p-2 text-muted-foreground transition-colors hover:text-foreground hover:bg-accent"
                        aria-label="Alternar tema"
                    >
                        <Sun v-if="appearance === 'dark'" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>
                </nav>
            </div>
        </header>

        <!-- Efeito de "Glow" sutil no fundo para um toque moderno -->
        <div class="absolute top-0 left-1/2 z-0 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-primary/10 blur-[150px]" />

        <!--
          Container do conteúdo com z-index para ficar sobre o 'glow' e animações de entrada.
          'animate-in' é uma classe do Tailwind que ativa as animações.
        -->
        <div class="relative z-10 flex flex-1 items-center justify-center p-6">
            <div class="max-w-3xl text-center animate-in fade-in slide-in-from-bottom-12 duration-700">

                <!-- Logo com animação de zoom-in -->
                <img
                    src="/transparent-logo.svg"
                    alt="Logo Tana"
                    class="mx-auto h-20 w-auto animate-in fade-in zoom-in-75 delay-300 duration-500"
                />

                <!-- Pre-title com animação de entrada -->
                <p class="mt-8 text-sm font-medium uppercase tracking-wider text-primary animate-in fade-in slide-in-from-bottom-4 delay-500 duration-500">
                    Sistema de reservas inteligente
                </p>

                <!-- Título principal com efeito de texto em gradiente e animação -->
                <h1 class="mt-4 text-6xl font-extrabold tracking-tighter bg-gradient-to-br from-foreground to-foreground/60 bg-clip-text text-transparent animate-in fade-in slide-in-from-bottom-4 delay-700 duration-500">
                    A mesa do seu restaurante
                </h1>
                <h2 class="text-6xl font-extrabold tracking-tighter text-muted-foreground animate-in fade-in slide-in-from-bottom-4 delay-900 duration-500">
                    começa aqui
                </h2>

                <!-- Descrição com animação -->
                <p class="mx-auto mt-6 max-w-xl text-lg text-muted-foreground animate-in fade-in slide-in-from-bottom-4 delay-1000 duration-500">
                    TANA simplifica reservas para restaurantes com elegância e eficiência. Gerencie mesas, horários e clientes em uma plataforma moderna.
                </p>

                <!-- Botões com animações e estilo aprimorado (sombras, efeito de escala no hover) -->
                <div class="mt-10 flex flex-wrap justify-center gap-4 animate-in fade-in slide-in-from-bottom-8 delay-[1200ms] duration-500">
                    <Link
                        href="/login"
                        class="transform rounded-lg bg-primary px-8 py-3 text-base font-semibold text-primary-foreground shadow-lg transition-all hover:scale-105 hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
                    >
                        Entrar
                    </Link>
                    <Link
                        v-if="canRegister"
                        href="/register"
                        class="transform rounded-lg border border-border bg-card px-8 py-3 text-base font-semibold text-foreground backdrop-blur-sm transition-all hover:scale-105 hover:bg-accent focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-background"
                    >
                        Criar Conta
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
