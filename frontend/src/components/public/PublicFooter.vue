<script setup lang="ts"></script>

<template>
    <footer class="bg-gray-900 text-white py-16 min-h-[400px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="relative">
                            <i class="pi pi-shield text-primary-400 text-4xl"></i>
                            <i class="pi pi-shield text-primary-400 text-2xl absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"></i>
                        </div>
                        <span class="text-2xl font-bold">SeuRacha</span>
                    </div>
                    <p class="text-gray-400">Transforme cada partida em uma experiência inesquecível. Descubra arenas premium, compare opções e reserve instantaneamente o seu palco perfeito para a vitória.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-primary-400">Empresa</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-info-circle mr-2"></i>Sobre Nós</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-cogs mr-2"></i>Como Funciona</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-file-contract mr-2"></i>Termos de Uso</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-shield-alt mr-2"></i>Privacidade</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-primary-400">Proprietários</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-plus-circle mr-2"></i>Anunciar Arena</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-edit mr-2"></i>Gerenciar Anúncios</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-question-circle mr-2"></i>Centro de Ajuda</a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-lightbulb mr-2"></i>Dicas</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-primary-400">Contato</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2"></i>
                            <a href="mailto:seuracha@seuracha.com" class="hover:text-white transition-colors">seurachacontato@gmail.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-bug mr-2"></i>
                            <a href="mailto:seuracha@seuracha.com" class="hover:text-white transition-colors">seuracha@seuracha.com (Sugestão ou Bug)</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-instagram mr-2"></i>
                            <a href="https://www.instagram.com/seuracha?igsh=MXQydGp3MmRkYW5uOA==" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">@seuracha</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Fortaleza, CE
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4 text-primary-400">Tecnologias</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center">
                            <i class="pi pi-code mr-2"></i>
                            <a href="https://primevue.org/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">PrimeVue</a>
                        </li>
                        <li class="flex items-center">
                            <i class="pi pi-palette mr-2"></i>
                            <a href="https://github.com/primefaces/sakai-vue" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors">Tema Sakai</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ new Date().getFullYear() }} SeuRacha. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</template>

<style scoped></style>
