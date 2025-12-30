<script setup lang="ts">
import { useHead } from '@vueuse/head';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import Card from 'primevue/card';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicTopbar from '@/components/public/PublicTopbar.vue';

useHead({
    title: 'Como Funciona o SeuRacha - Plataforma para Atletas e Proprietários de Campos',
    meta: [
        {
            name: 'description',
            content: 'Descubra como o SeuRacha funciona para atletas e proprietários de campos. Sistema completo de reservas esportivas, perfis de atletas e gestão automatizada de campos.'
        },
        {
            name: 'keywords',
            content: 'como funciona SeuRacha, reservas esportivas, atletas, proprietários campos, plataforma esportiva, reservas online, gestão campos'
        },
        { property: 'og:title', content: 'Como Funciona o SeuRacha - Plataforma para Atletas e Proprietários' },
        {
            property: 'og:description',
            content: 'Sistema completo para reservas de campos esportivos e gestão para proprietários. Perfil de atletas e comunidade esportiva.'
        },
        { property: 'og:type', content: 'website' },
        { name: 'twitter:card', content: 'summary' },
        { name: 'twitter:title', content: 'Como Funciona o SeuRacha' },
        {
            name: 'twitter:description',
            content: 'Plataforma completa para atletas e proprietários de campos esportivos.'
        }
    ]
});

const router = useRouter();

const athleteFeatures = ref([
    {
        icon: 'pi pi-search',
        title: 'Encontre Campos Facilmente',
        description: 'Use nossa busca avançada para encontrar campos próximos a você. Filtre por esporte, horário, preço e muito mais.',
        color: 'bg-green-500'
    },
    {
        icon: 'pi pi-calendar',
        title: 'Reserve em Segundos',
        description: 'Sistema de reserva simples e rápido. Escolha o horário, confirme e pronto! Sem complicações.',
        color: 'bg-blue-500'
    },
    {
        icon: 'pi pi-chart-line',
        title: 'Acompanhe sua Evolução',
        description: 'Monitore suas estatísticas, histórico de jogos e melhore seu desempenho com dados detalhados.',
        color: 'bg-purple-500'
    },
    {
        icon: 'pi pi-users',
        title: 'Conecte-se com Atletas',
        description: 'Encontre parceiros de treino, forme equipes e participe de uma comunidade esportiva ativa.',
        color: 'bg-orange-500'
    },
    {
        icon: 'pi pi-mobile',
        title: 'Acesso Web',
        description: 'Plataforma web responsiva que funciona perfeitamente no seu celular, tablet ou computador.',
        color: 'bg-pink-500'
    },
    {
        icon: 'pi pi-shield',
        title: 'Pagamento Seguro',
        description: 'Sistema de pagamento integrado e seguro. Suas transações protegidas com criptografia avançada.',
        color: 'bg-indigo-500'
    }
]);

const athleteProfile = ref({
    name: 'João Silva',
    age: 25,
    sport: 'Futebol',
    position: 'Meio-Campo',
    dominantSide: 'Direito',
    height: '1.75m',
    weight: '70kg',
    gamesPlayed: 48,
    goalsScored: 12,
    assists: 8,
    rating: 4.7
});

const statistics = ref([
    { label: 'Jogos Disputados', value: '48', icon: 'fas fa-play', color: 'text-green-600' },
    { label: 'Gols Marcados', value: '12', icon: 'fas fa-trophy', color: 'text-blue-600' },
    { label: 'Assistências', value: '8', icon: 'fas fa-handshake', color: 'text-purple-600' },
    { label: 'Avaliação Média', value: '4.7', icon: 'fas fa-star', color: 'text-yellow-600' }
]);

const howItWorks = ref([
    {
        step: '1',
        title: 'Cadastre-se',
        description: 'Crie sua conta gratuita e complete seu perfil de atleta com suas informações esportivas.',
        icon: 'pi pi-user-plus'
    },
    {
        step: '2',
        title: 'Busque Campos',
        description: 'Use nossa ferramenta de busca para encontrar campos disponíveis na sua região.',
        icon: 'pi pi-search'
    },
    {
        step: '3',
        title: 'Faça sua Reserva',
        description: 'Selecione o horário desejado e confirme sua reserva de forma rápida e segura.',
        icon: 'pi pi-calendar'
    },
    {
        step: '4',
        title: 'Jogue e Evolua',
        description: 'Aproveite seu jogo e acompanhe suas estatísticas para melhorar continuamente.',
        icon: 'pi pi-chart-line'
    }
]);

const navigateToRegister = () => {
    router.push({ name: 'register' });
};

const copyToClipboard = async (text: string) => {
    try {
        await navigator.clipboard.writeText(text);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <PublicTopbar />

        <section class="bg-primary text-white py-20">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center bg-white text-gray-900 text-sm font-medium px-4 py-2 rounded-full mb-6">
                        <i class="pi pi-star mr-2"></i>
                        Sua Jornada Esportiva Começa Aqui
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Como Funciona para
                        <span class="text-white">Atletas</span>
                    </h1>
                    <p class="text-xl text-white leading-relaxed mb-8 max-w-3xl mx-auto">Descubra como milhares de atletas estão revolucionando a forma de encontrar campos, fazer reservas e acompanhar sua evolução esportiva na nossa plataforma.</p>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Como Funciona</h2>
                        <p class="text-xl text-gray-600">4 passos simples para começar sua jornada</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="step in howItWorks" :key="step.step" class="text-center">
                            <div class="relative mb-6">
                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i :class="step.icon" class="text-white text-xl"></i>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    {{ step.step }}
                                </div>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ step.title }}</h3>
                            <p class="text-gray-600">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Perfil de Atleta</h2>
                        <p class="text-xl text-gray-600">Veja como fica o seu perfil completo na plataforma</p>
                    </div>

                    <div class="grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-1">
                            <Card class="!shadow-lg !border-0">
                                <template #content>
                                    <div class="text-center">
                                        <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="pi pi-user text-white text-3xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ athleteProfile.name }}</h3>
                                        <p class="text-gray-600 mb-4">{{ athleteProfile.age }} anos</p>

                                        <div class="space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Esporte:</span>
                                                <span class="font-medium">{{ athleteProfile.sport }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Posição:</span>
                                                <span class="font-medium">{{ athleteProfile.position }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Dominância:</span>
                                                <span class="font-medium">{{ athleteProfile.dominantSide }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Altura:</span>
                                                <span class="font-medium">{{ athleteProfile.height }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Peso:</span>
                                                <span class="font-medium">{{ athleteProfile.weight }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div class="lg:col-span-2">
                            <Card class="!shadow-lg !border-0 h-full">
                                <template #content>
                                    <h4 class="text-xl font-bold text-gray-900 mb-6">Estatísticas do Atleta</h4>
                                    <div class="grid sm:grid-cols-2 gap-6">
                                        <div v-for="stat in statistics" :key="stat.label" class="bg-gray-50 p-6 rounded-lg">
                                            <div class="flex items-center justify-between mb-3">
                                                <i :class="[stat.icon, stat.color]" class="text-2xl"></i>
                                                <span class="text-3xl font-bold text-gray-900">{{ stat.value }}</span>
                                            </div>
                                            <p class="text-gray-600 font-medium">{{ stat.label }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h5 class="text-lg font-semibold text-gray-900 mb-2">Avaliação Geral</h5>
                                                <p class="text-gray-600">Baseada no desempenho e feedback dos jogos</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-4xl font-bold text-green-600">{{ athleteProfile.rating }}</div>
                                                <div class="flex justify-end mt-1">
                                                    <i v-for="n in 5" :key="n" :class="n <= Math.floor(athleteProfile.rating) ? 'text-yellow-400' : 'text-gray-300'" class="pi pi-star text-sm"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Perfil Completo do Atleta</h2>
                        <p class="text-xl text-gray-600">Veja todas as funcionalidades disponíveis no seu perfil personalizado</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-4">
                            <Card class="!shadow-xl !border-0 h-full">
                                <template #content>
                                    <div class="text-center">
                                        <div class="w-32 h-32 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                            <i class="pi pi-user text-white text-5xl"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">João Silva</h3>
                                        <p class="text-lg text-green-600 font-semibold mb-4">Atacante</p>

                                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg mb-6">
                                            <div class="flex justify-center mb-2">
                                                <i v-for="n in 5" :key="n" class="pi pi-star text-yellow-400 text-lg"></i>
                                            </div>
                                            <div class="text-2xl font-bold text-gray-900">4.8</div>
                                            <div class="text-sm text-gray-600">Avaliação Geral</div>
                                        </div>

                                        <div class="border-t pt-4 space-y-3">
                                            <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Contato</h4>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 flex items-center gap-2">
                                                    <i class="pi pi-tag text-xs text-green-600"></i>
                                                    Id público:
                                                </span>
                                                <span class="font-semibold text-gray-800 text-sm">AST-2024-001</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 flex items-center gap-2">
                                                    <i class="pi pi-envelope text-green-600"></i>
                                                    Email:
                                                </span>
                                                <span class="font-semibold text-gray-800 text-sm">joao@email.com</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 flex items-center gap-2">
                                                    <i class="pi pi-phone text-green-600"></i>
                                                    Telefone:
                                                </span>
                                                <span class="font-semibold text-gray-800 text-sm">(11) 99999-9999</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div class="lg:col-span-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <Card class="!shadow-lg !border-0 h-full">
                                    <template #content>
                                        <div class="p-6">
                                            <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-4">
                                                <i class="pi pi-trophy text-green-600"></i>
                                                Informações Esportivas
                                            </h4>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="fa-solid fa-medal text-xs text-green-600"></i>
                                                        Esporte:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Futebol</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-circle-fill text-xs text-green-600"></i>
                                                        Posição Principal:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Atacante</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-circle text-xs text-green-600"></i>
                                                        Posição Secundária:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Meia-atacante</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-check-circle text-green-600"></i>
                                                        Especialidade:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Finalização</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-shield text-green-600"></i>
                                                        Clube:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">São Paulo FC</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-calendar text-green-600"></i>
                                                        Total de Jogos:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">47</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </Card>

                                <Card class="!shadow-lg !border-0 h-full">
                                    <template #content>
                                        <div class="p-6">
                                            <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-4">
                                                <i class="pi pi-user text-blue-600"></i>
                                                Características Físicas
                                            </h4>
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="fa-solid fa-venus-mars text-xs text-blue-600"></i>
                                                        Gênero:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Masculino</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-arrows-alt text-xs text-blue-600"></i>
                                                        Altura:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">1.78m</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-balance-scale text-xs text-blue-600"></i>
                                                        Peso:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">75kg</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-hand-paper text-xs text-blue-600"></i>
                                                        Dominância:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">Destro</span>
                                                </div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <span class="text-sm text-gray-600 flex items-center gap-2">
                                                        <i class="pi pi-birthday-cake text-xs text-blue-600"></i>
                                                        Data de Nascimento:
                                                    </span>
                                                    <span class="font-semibold text-gray-800">15/03/1995</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </Card>
                            </div>

                            <Card class="!shadow-lg !border-0">
                                <template #content>
                                    <div class="p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-6">
                                            <i class="pi pi-chart-bar text-purple-600"></i>
                                            Estatísticas
                                        </h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-soccer text-2xl text-green-600 mb-2"></i>
                                                <div class="text-xl font-bold text-green-600 mb-1">47</div>
                                                <div class="text-sm text-gray-600">Jogos</div>
                                            </div>
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-trophy text-2xl text-yellow-600 mb-2"></i>
                                                <div class="text-xl font-bold text-yellow-600 mb-1">23</div>
                                                <div class="text-sm text-gray-600">Gols</div>
                                            </div>
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-star text-2xl text-blue-600 mb-2"></i>
                                                <div class="text-xl font-bold text-blue-600 mb-1">15</div>
                                                <div class="text-sm text-gray-600">Assistências</div>
                                            </div>
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-thumbs-up text-2xl text-purple-600 mb-2"></i>
                                                <div class="text-xl font-bold text-purple-600 mb-1">8.2</div>
                                                <div class="text-sm text-gray-600">Nota Média</div>
                                            </div>
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-clock text-2xl text-orange-600 mb-2"></i>
                                                <div class="text-xl font-bold text-orange-600 mb-1">120</div>
                                                <div class="text-sm text-gray-600">Minutos</div>
                                            </div>
                                            <div class="text-center p-4 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                                                <i class="pi pi-check-circle text-2xl text-green-600 mb-2"></i>
                                                <div class="text-xl font-bold text-green-600 mb-1">85%</div>
                                                <div class="text-sm text-gray-600">Aproveitamento</div>
                                            </div>
                                        </div>

                                        <div class="mt-6 text-center">
                                            <p class="text-gray-500 text-sm">Participe de rachas para gerar suas estatísticas completas!</p>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Gestão Completa de Reservas</h2>
                        <p class="text-xl text-gray-600">Gerencie participantes, times, estatísticas e avaliações em uma única plataforma</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div class="space-y-8">
                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="pi pi-users text-blue-600 text-2xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Participantes</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Total de Participantes:</span>
                                        <span class="font-bold text-blue-600">8</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Confirmados:</span>
                                        <span class="font-bold text-green-600">6</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Pagos:</span>
                                        <span class="font-bold text-green-600">5</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="text-gray-600">Valor Total:</span>
                                        <span class="font-bold text-green-600">R$ 240,00</span>
                                    </div>
                                </div>
                                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-800">Gerencie participantes, controle pagamentos e compartilhe informações via WhatsApp.</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="pi pi-shield text-green-600 text-2xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Times</h3>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">1. Sorteio Automático de Times</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                                <h5 class="font-semibold text-green-800 mb-2">Time A</h5>
                                                <div class="space-y-1">
                                                    <p class="text-sm text-green-700">João Silva</p>
                                                    <p class="text-sm text-green-700">Carlos Santos</p>
                                                    <p class="text-sm text-green-700">Pedro Lima</p>
                                                </div>
                                            </div>
                                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                                <h5 class="font-semibold text-blue-800 mb-2">Time B</h5>
                                                <div class="space-y-1">
                                                    <p class="text-sm text-blue-700">Lucas Costa</p>
                                                    <p class="text-sm text-blue-700">Marcos Oliveira</p>
                                                    <p class="text-sm text-blue-700">Rafael Souza</p>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">Times sorteados automaticamente dos participantes confirmados</p>
                                    </div>

                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">2. Amistosos entre Times Estabelecidos</h4>
                                        <div class="bg-gradient-to-r from-blue-50 to-red-50 p-4 rounded-lg">
                                            <div class="flex items-center justify-center gap-4 mb-3">
                                                <div class="text-center">
                                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2">
                                                        <i class="pi pi-users text-white"></i>
                                                    </div>
                                                    <h5 class="font-semibold text-blue-800">Time Azul FC</h5>
                                                    <p class="text-sm text-blue-600">Time da Casa</p>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-900">VS</div>
                                                    <div class="text-sm text-gray-600">Amistoso</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mb-2">
                                                        <i class="pi pi-users text-white"></i>
                                                    </div>
                                                    <h5 class="font-semibold text-red-800">Time Vermelho FC</h5>
                                                    <p class="text-sm text-red-600">Time Visitante</p>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-xl font-bold text-gray-900 mb-2">2 - 1</div>
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                                    <i class="pi pi-trophy"></i>
                                                    <span>Time Azul FC Venceu!</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">Jogos oficiais entre times criados e gerenciados no sistema</p>
                                    </div>
                                </div>
                                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-800">Duas formas de organizar jogos: sorteio automático para rachas informais ou amistosos entre times estabelecidos.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="pi pi-chart-bar text-purple-600 text-2xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Estatísticas</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 mb-1">23</div>
                                            <div class="text-sm text-purple-700">Gols</div>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 mb-1">15</div>
                                            <div class="text-sm text-purple-700">Assistências</div>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 mb-1">8.5</div>
                                            <div class="text-sm text-purple-700">Nota Média</div>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 mb-1">85%</div>
                                            <div class="text-sm text-purple-700">Aproveitamento</div>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h5 class="font-semibold text-gray-900 mb-2">Por Jogador</h5>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">João Silva:</span>
                                                <span class="text-sm font-medium">5 gols, 3 assistências</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Carlos Santos:</span>
                                                <span class="text-sm font-medium">4 gols, 2 assistências</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-gray-600">Pedro Lima:</span>
                                                <span class="text-sm font-medium">3 gols, 1 assistência</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 p-4 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-purple-800">Registre estatísticas detalhadas da partida com métricas personalizáveis.</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="pi pi-star text-orange-600 text-2xl"></i>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Avaliações</h3>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                                            <div class="flex justify-center mb-2">
                                                <i v-for="n in 5" :key="n" class="pi pi-star-fill text-yellow-400"></i>
                                            </div>
                                            <div class="text-lg font-bold text-orange-600">9.2</div>
                                            <div class="text-sm text-orange-700">Nota Geral</div>
                                        </div>
                                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                                            <div class="text-lg font-bold text-orange-600 mb-1">6</div>
                                            <div class="text-sm text-orange-700">Avaliações</div>
                                        </div>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <h5 class="font-semibold text-gray-900 mb-2">Aspectos Avaliados</h5>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Técnico:</span>
                                                <span class="font-medium">8.5</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Tático:</span>
                                                <span class="font-medium">9.0</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Físico:</span>
                                                <span class="font-medium">8.8</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Mental:</span>
                                                <span class="font-medium">9.2</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6 p-4 bg-orange-50 rounded-lg">
                                    <p class="text-sm text-orange-800">Sistema completo de avaliações com comentários e métricas detalhadas.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-16 text-center">
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Tudo em Uma Plataforma</h3>
                            <p class="text-gray-600 mb-6">Gerencie todo o ciclo da sua partida: da reserva até as avaliações finais, tudo organizado e acessível.</p>
                            <div class="grid md:grid-cols-4 gap-6">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-calendar text-blue-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Reserve</h4>
                                    <p class="text-sm text-gray-600">Campo e horário</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-users text-green-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Convide</h4>
                                    <p class="text-sm text-gray-600">Participantes</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-play text-purple-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Jogue</h4>
                                    <p class="text-sm text-gray-600">E registre stats</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-star text-orange-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Avalie</h4>
                                    <p class="text-sm text-gray-600">Performance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Gerenciamento Completo de Times</h2>
                        <p class="text-xl text-gray-600">Crie, gerencie e organize seu time com ferramentas profissionais</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                        <div class="lg:col-span-12">
                            <Card class="!shadow-xl !border-0">
                                <template #content>
                                    <div class="relative overflow-hidden rounded-2xl shadow-xl mb-8" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%)">
                                        <div class="absolute inset-0 bg-black bg-opacity-10">
                                            <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                                <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                                    <path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5" />
                                                </pattern>
                                                <rect width="100" height="100" fill="url(#grid)" />
                                            </svg>
                                        </div>

                                        <div class="relative px-6 py-12 lg:px-8">
                                            <div class="max-w-7xl mx-auto">
                                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                                                    <div class="flex justify-center lg:justify-start">
                                                        <div class="relative group">
                                                            <div
                                                                class="w-32 h-32 lg:w-40 lg:h-40 rounded-3xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-2xl ring-4 ring-white/30 group-hover:ring-white/50 transition-all duration-300 transform group-hover:scale-105"
                                                            >
                                                                <i class="pi pi-shield text-5xl lg:text-6xl text-white/80"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="lg:col-span-2 text-center lg:text-left">
                                                        <div class="mb-4">
                                                            <h1 class="text-3xl lg:text-5xl font-bold text-white mb-2 leading-tight">Time Exemplo FC</h1>
                                                            <p class="text-xl lg:text-2xl text-white/90 font-medium">"Os Campeões"</p>
                                                            <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                                                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                                                                    <i class="pi pi-trophy text-white text-sm"></i>
                                                                    <span class="text-white font-medium text-sm">Futebol</span>
                                                                </div>
                                                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                                                                    <i class="pi pi-users text-white text-sm"></i>
                                                                    <span class="text-white font-medium text-sm">22/30</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center justify-center lg:justify-start gap-3 flex-wrap">
                                                            <Button label="Editar Time" icon="pi pi-pencil" class="bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm transition-all duration-200" />
                                                            <Button icon="pi pi-external-link" class="p-button-rounded bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm" v-tooltip="'Website'" />
                                                            <Button icon="pi pi-search-plus" class="p-button-rounded bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm" v-tooltip="'Ampliar escudo'" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-12 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-6 items-stretch -mx-4 sm:mx-0">
                                <Card class="w-full h-full flex flex-col rounded-none sm:rounded-xl">
                                    <template #header>
                                        <div class="p-4 lg:p-6 pb-0">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                                <i class="pi pi-home text-blue-600"></i>
                                                Informações do Time
                                            </h3>
                                        </div>
                                    </template>
                                    <template #content>
                                        <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                            <div class="flex justify-between items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" @click="copyToClipboard('AST-TEAM-2024-001')">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <span class="relative inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10 shadow-sm">
                                                        <i class="pi pi-hashtag text-xs text-blue-600"></i>
                                                    </span>
                                                    Id público:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">AST-TEAM-2024-001</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-tag text-xs text-blue-600"></i>
                                                    Nome:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">Time Exemplo FC</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-map-marker text-xs text-blue-600"></i>
                                                    Estádio:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">Estádio Municipal</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-calendar text-xs text-blue-600"></i>
                                                    Fundação:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">15/08/2020</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-globe text-xs text-blue-600"></i>
                                                    Website:
                                                </span>
                                                <Button label="Visitar" icon="pi pi-external-link" size="small" class="p-button-primary p-button-sm" />
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-eye text-xs text-blue-600"></i>
                                                    Visibilidade:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">Público</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-users text-xs text-blue-600"></i>
                                                    Tipo do time:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">Amador</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-user text-xs text-blue-600"></i>
                                                    Fundado por:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">João Silva</span>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-clock text-xs text-blue-600"></i>
                                                    Membros:
                                                </span>
                                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">22/30</span>
                                            </div>
                                        </div>
                                    </template>
                                </Card>

                                <Card class="w-full h-full flex flex-col rounded-none sm:rounded-xl">
                                    <template #header>
                                        <div class="p-4 lg:p-6 pb-0">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                                <i class="pi pi-palette text-purple-600"></i>
                                                Cores e Design
                                            </h3>
                                        </div>
                                    </template>
                                    <template #content>
                                        <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-circle-fill text-xs text-blue-600"></i>
                                                    Cor Primária:
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-200">#3b82f6</span>
                                                    <div style="background-color: #3b82f6" class="w-8 h-8 rounded-md border-2 border-gray-300 shadow-sm"></div>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-center gap-2">
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                                    <i class="pi pi-circle text-xs text-blue-600"></i>
                                                    Cor Secundária:
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-200">#1e40af</span>
                                                    <div style="background-color: #1e40af" class="w-8 h-8 rounded-md border-2 border-gray-300 shadow-sm"></div>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div class="text-center">
                                                    <div style="background-color: #3b82f6" class="w-full h-12 rounded-lg border-2 border-gray-300 shadow-sm flex items-center justify-center">
                                                        <span class="text-white font-semibold text-xs">#3b82f6</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Prévia</p>
                                                </div>
                                                <div class="text-center">
                                                    <div style="background-color: #1e40af" class="w-full h-12 rounded-lg border-2 border-gray-300 shadow-sm flex items-center justify-center">
                                                        <span class="text-white font-semibold text-xs">#1e40af</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Prévia</p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </Card>
                            </div>

                            <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                                <template #header>
                                    <div class="p-4 lg:p-6 pb-0">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                            <i class="pi pi-book text-green-600"></i>
                                            Sobre o Time
                                        </h3>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="px-4 lg:px-6 pb-4 lg:pb-6">
                                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm lg:text-base">
                                            O Time Exemplo FC é um clube de futebol dedicado ao desenvolvimento de atletas e à promoção do esporte. Fundado em 2020, o clube conquistou diversos títulos regionais, sendo conhecido como "Os Campeões"
                                            devido às suas cores azul e branco. Nossa missão é formar atletas completos e promover a prática saudável do futebol em nossa comunidade.
                                        </p>
                                    </div>
                                </template>
                            </Card>

                            <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                                <template #header>
                                    <div class="p-4 lg:p-6 pb-0">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                            <i class="pi pi-chart-bar text-orange-600"></i>
                                            Estatísticas
                                        </h3>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="px-4 lg:px-6 pb-4 lg:pb-6">
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 lg:gap-4">
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-play text-lg lg:text-xl text-green-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">38</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Partidas</div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-trophy text-lg lg:text-xl text-yellow-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">28</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Vitórias</div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-times text-lg lg:text-xl text-red-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">6</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Derrotas</div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-pause-one text-lg lg:text-xl text-blue-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">4</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Empates</div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-plus text-lg lg:text-xl text-green-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">89</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Gols Marcados</div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                                <i class="pi pi-minus text-lg lg:text-xl text-red-500 mb-1 lg:mb-2"></i>
                                                <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">32</div>
                                                <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">Gols Sofridos</div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                                <template #header>
                                    <div class="p-4 lg:p-6 pb-0">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                            <i class="pi pi-trophy text-yellow-600"></i>
                                            Últimas Partidas
                                        </h3>
                                    </div>
                                </template>
                                <template #content>
                                    <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-3">
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center gap-4 flex-1">
                                                <div class="text-center min-w-fit">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">15/12/2024</p>
                                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Casa</p>
                                                </div>
                                                <div class="flex-1 text-center">
                                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">vs Time A FC</p>
                                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">3 x 1</p>
                                                </div>
                                                <div class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Vitória</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center gap-4 flex-1">
                                                <div class="text-center min-w-fit">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">12/12/2024</p>
                                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Fora</p>
                                                </div>
                                                <div class="flex-1 text-center">
                                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">vs Time B FC</p>
                                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">2 x 0</p>
                                                </div>
                                                <div class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Vitória</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                            <div class="text-center min-w-fit">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">08/12/2024</p>
                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Casa</p>
                                            </div>
                                            <div class="flex-1 text-center">
                                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">vs Time C FC</p>
                                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">1 x 1</p>
                                            </div>
                                            <div class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Empate</div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                        <template #content>
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <i class="pi pi-users text-blue-600"></i>
                                        Elenco (22 jogadores)
                                    </h3>
                                </div>
                                <Button label="Adicionar Jogador(a)" icon="pi pi-plus" size="small" class="p-button-primary" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border-2 border-blue-200 bg-white dark:bg-gray-800">
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold shadow-sm bg-green-100 text-green-800"> Ativo </span>
                                    </div>

                                    <div class="p-5 pt-12">
                                        <div class="flex flex-col items-center mb-4">
                                            <div class="relative w-20 h-20 flex items-center justify-center mb-3 rounded-full shadow-lg bg-gradient-to-br from-blue-400 to-blue-600">
                                                <i class="fa-solid fa-shirt text-white text-5xl"></i>
                                                <span class="absolute font-black text-2xl drop-shadow-lg text-black"> 10 </span>
                                            </div>

                                            <h4 class="font-bold text-lg text-center text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">João Silva</h4>

                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 capitalize">Atacante</p>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-map-marker text-xs text-blue-600"></i>
                                                    Posição:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Centroavante </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-directions text-xs text-blue-600"></i>
                                                    Lado dominante:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Direito </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-calendar text-xs text-blue-600"></i>
                                                    No time desde:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100"> 01/2023 </span>
                                            </div>
                                        </div>

                                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <Button icon="pi pi-pencil" label="Editar" size="small" class="flex-1 p-button-outlined" />
                                        </div>
                                    </div>
                                </div>

                                <div class="relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border-2 border-blue-200 bg-white dark:bg-gray-800">
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold shadow-sm bg-green-100 text-green-800"> Ativo </span>
                                    </div>

                                    <div class="p-5 pt-12">
                                        <div class="flex flex-col items-center mb-4">
                                            <div class="relative w-20 h-20 flex items-center justify-center mb-3 rounded-full shadow-lg bg-gradient-to-br from-blue-400 to-blue-600">
                                                <i class="fa-solid fa-shirt text-white text-5xl"></i>
                                                <span class="absolute font-black text-2xl drop-shadow-lg text-black"> 7 </span>
                                            </div>

                                            <h4 class="font-bold text-lg text-center text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">Carlos Santos</h4>

                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 capitalize">Meio-campista</p>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-map-marker text-xs text-blue-600"></i>
                                                    Posição:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Volante </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-directions text-xs text-blue-600"></i>
                                                    Lado dominante:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Direito </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-calendar text-xs text-blue-600"></i>
                                                    No time desde:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100"> 06/2022 </span>
                                            </div>
                                        </div>

                                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <Button icon="pi pi-pencil" label="Editar" size="small" class="flex-1 p-button-outlined" />
                                        </div>
                                    </div>
                                </div>

                                <div class="relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border-2 border-blue-200 bg-white dark:bg-gray-800">
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold shadow-sm bg-yellow-100 text-yellow-800"> Reserva </span>
                                    </div>

                                    <div class="p-5 pt-12">
                                        <div class="flex flex-col items-center mb-4">
                                            <div class="relative w-20 h-20 flex items-center justify-center mb-3 rounded-full shadow-lg bg-gradient-to-br from-blue-400 to-blue-600">
                                                <i class="fa-solid fa-shirt text-white text-5xl"></i>
                                                <span class="absolute font-black text-2xl drop-shadow-lg text-black"> 23 </span>
                                            </div>

                                            <h4 class="font-bold text-lg text-center text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">Pedro Lima</h4>

                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 capitalize">Goleiro</p>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-map-marker text-xs text-blue-600"></i>
                                                    Posição:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Goleiro </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-directions text-xs text-blue-600"></i>
                                                    Lado dominante:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize"> Direito </span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                                <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                                    <i class="pi pi-calendar text-xs text-blue-600"></i>
                                                    No time desde:
                                                </span>
                                                <span class="font-semibold text-gray-900 dark:text-gray-100"> 03/2024 </span>
                                            </div>
                                        </div>

                                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <Button icon="pi pi-pencil" label="Editar" size="small" class="flex-1 p-button-outlined" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 text-center">
                                <p class="text-gray-500 text-sm">Mostrando 3 de 22 jogadores • <Button label="Ver Todos" link class="p-0 text-blue-600" /></p>
                            </div>
                        </template>
                    </Card>

                    <div class="mt-16 text-center">
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl shadow-lg p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Ferramentas de Gestão Completa</h3>
                            <p class="text-gray-600 mb-6">Gerencie todas as aspectos do seu time em uma única plataforma</p>
                            <div class="grid md:grid-cols-4 gap-6">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-plus text-blue-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Criar Time</h4>
                                    <p class="text-sm text-gray-600">Configure cores, escudo e informações</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-users text-green-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Gerenciar Jogadores</h4>
                                    <p class="text-sm text-gray-600">Adicione, edite e organize o elenco</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-chart-bar text-purple-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Acompanhar Estatísticas</h4>
                                    <p class="text-sm text-gray-600">Métricas detalhadas de performance</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="pi pi-trophy text-orange-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">Registrar Partidas</h4>
                                    <p class="text-sm text-gray-600">Histórico completo de jogos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4">Principais Funcionalidades</h2>
                        <p class="text-xl text-gray-600">Tudo que você precisa para uma experiência esportiva completa</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="feature in athleteFeatures" :key="feature.title" class="group hover:scale-105 transition-transform duration-300">
                            <Card class="!shadow-lg !border-0 h-full">
                                <template #content>
                                    <div class="text-center">
                                        <div :class="feature.color" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i :class="feature.icon" class="text-white text-xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ feature.title }}</h3>
                                        <p class="text-gray-600 leading-relaxed">{{ feature.description }}</p>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-gray-900 text-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center text-white">
                    <h2 class="text-4xl font-bold mb-6 text-white">Pronto para Começar?</h2>
                    <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">Junte-se a milhares de atletas que já estão usando nossa plataforma para encontrar campos, melhorar seu desempenho e conectar-se com a comunidade esportiva.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Button @click="navigateToRegister" class="!bg-white !text-green-600 !border-white !px-8 !py-4 !text-lg font-semibold hover:!bg-green-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="pi pi-user-plus mr-2"></i>
                            Cadastre-se Gratuitamente
                        </Button>
                    </div>
                </div>
            </div>
        </section>
        <PublicFooter />
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
}

/* Animações suaves */
.transition-transform {
    transition: transform 0.3s ease;
}

.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Classes do Tailwind para cores primárias */
.text-green-300 {
    color: #86efac;
}

.text-green-100 {
    color: #dcfce7;
}

.text-green-600 {
    color: #16a34a;
}

.bg-green-500 {
    background-color: #22c55e;
}

.bg-green-600 {
    background-color: #16a34a;
}

.bg-green-50 {
    background-color: #f0fdf4;
}

.hover\:bg-green-50:hover {
    background-color: #f0fdf4;
}

.hover\:bg-green-600:hover {
    background-color: #16a34a;
}

/* Cores para as estatísticas */
.text-blue-600 {
    color: #2563eb;
}

.text-purple-600 {
    color: #9333ea;
}

.text-yellow-600 {
    color: #ca8a04;
}

.text-yellow-400 {
    color: #facc15;
}
</style>
