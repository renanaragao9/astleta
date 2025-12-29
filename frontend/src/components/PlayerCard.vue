<script setup lang="ts">
import { computed, ref } from 'vue';
import Dialog from 'primevue/dialog';

interface Props {
    imagePath?: string;
    athleteName?: string;
    rating?: number;
    position?: string;
    nationFlag?: string;
    clubBadge?: string;
    stats?: {
        pac: number;
        sho: number;
        pas: number;
        dri: number;
        def: number;
        tea: number;
    };
    extra?: string[];
}

const props = defineProps<Props>();

const modalOpen = ref(false);
const modalImage = ref('');

const openModal = (src: string) => {
    modalImage.value = src;
    modalOpen.value = true;
};

const card = computed(() => {
    const r = props.rating || 0;
    if (r >= 9.5 && r <= 10) return 'card4';
    else if (r <= 4) return 'card1';
    else if (r <= 7) return 'card2';
    else return 'card3';
});

const textColor = computed(() => {
    const r = props.rating || 0;
    if (r >= 9.5 && r <= 10) return 'white';
    else if (r <= 4) return 'black';
    else return '#e9cc74';
});
</script>

<template>
    <div class="astleta-player-card" :style="{ backgroundImage: `url('/image/cards/${card}.png')` }">
        <div class="player-card-top" :style="{ color: textColor }">
            <div class="player-master-info">
                <div class="player-rating">
                    <span>{{ rating || 0 }}</span>
                </div>
                <div class="player-position">
                    <span>{{ position || 'ATA' }}</span>
                </div>
                <div class="player-nation" v-if="nationFlag">
                    <img :src="nationFlag" draggable="false" @click="openModal(nationFlag)" />
                </div>
                <div class="player-club" v-if="clubBadge">
                    <img :src="clubBadge" draggable="false" @click="openModal(clubBadge)" />
                </div>
            </div>
            <div class="player-picture">
                <img v-if="imagePath" :src="imagePath" :alt="`Foto de ${athleteName}`" draggable="false" @click="openModal(imagePath)" @error="(event) => ((event.target as HTMLImageElement).src = 'https://via.placeholder.com/300x400?text=Atleta')" />
                <img v-else src="https://via.placeholder.com/300x400?text=Atleta" :alt="`Foto de ${athleteName}`" draggable="false" @click="openModal('https://via.placeholder.com/300x400?text=Atleta')" />
                <div class="player-extra" v-if="extra && extra.length > 0">
                    <span v-for="item in extra" :key="item">{{ item }}</span>
                </div>
            </div>
        </div>
        <div class="player-card-bottom">
            <div class="player-info" :style="{ color: textColor }">
                <div class="player-name">
                    <span>{{ athleteName || 'ATLETA' }}</span>
                </div>
                <div class="player-features">
                    <div class="player-features-col">
                        <span>
                            <div class="player-feature-value">{{ stats?.sho || 0 }}</div>
                            <div class="player-feature-title">FIS</div>
                        </span>
                        <span>
                            <div class="player-feature-value">{{ stats?.pas || 0 }}</div>
                            <div class="player-feature-title">TAT</div>
                        </span>

                        <span>
                            <div class="player-feature-value">{{ stats?.def || 0 }}</div>
                            <div class="player-feature-title">TEC</div>
                        </span>
                    </div>
                    <div class="player-features-col">
                        <span>
                            <div class="player-feature-value">{{ stats?.dri || 0 }}</div>
                            <div class="player-feature-title">MEN</div>
                        </span>
                        <span>
                            <div class="player-feature-value">{{ stats?.pac || 0 }}</div>
                            <div class="player-feature-title">JOG</div>
                        </span>

                        <span>
                            <div class="player-feature-value">{{ stats?.tea || 0 }}</div>
                            <div class="player-feature-title">TRA</div>
                        </span>
                    </div>
                </div>
                <div class="player-logo">
                    <img src="/image/logo.png" alt="Logo" />
                </div>
            </div>
        </div>
    </div>
    <Dialog v-model:visible="modalOpen" modal>
        <img :src="modalImage" alt="Imagem ampliada" class="modal-image" />
    </Dialog>
</template>

<style scoped>
* {
    margin: 0;
    padding: 0;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}

.astleta-player-card {
    position: relative;
    width: 300px;
    height: 485px;
    background-position: center center;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    padding: 3.8rem 0;
    z-index: 2;
    -webkit-transition: 200ms ease-in;
    -o-transition: 200ms ease-in;
    transition: 200ms ease-in;
    margin: 0 auto;
    -o-object-fit: cover;
    object-fit: cover;
}

.astleta-player-card .player-card-top {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    color: #e9cc74;
    padding: 0 1.5rem;
}

.astleta-player-card .player-card-top .player-master-info {
    position: absolute;
    line-height: 2.2rem;
    font-weight: 300;
    padding: 1.5rem 0;
    text-transform: uppercase;
}

.astleta-player-card .player-card-top .player-master-info .player-rating {
    font-size: 2rem;
}

.astleta-player-card .player-card-top .player-master-info .player-position {
    font-size: 1.2rem;
}

.astleta-player-card .player-card-top .player-master-info .player-nation {
    display: block;
    width: 2rem;
    height: 25px;
    margin: 0.3rem 0;
}

.astleta-player-card .player-card-top .player-master-info .player-nation img {
    width: 100%;
    height: 100%;
    -o-object-fit: cover;
    object-fit: cover;
    cursor: pointer;
}

.astleta-player-card .player-card-top .player-master-info .player-club {
    display: block;
    width: 2.1rem;
    height: 40px;
}

.astleta-player-card .player-card-top .player-master-info .player-club img {
    width: 100%;
    height: 100%;
    -o-object-fit: cover;
    object-fit: cover;
    cursor: pointer;
}

.astleta-player-card .player-card-top .player-picture {
    width: 200px;
    height: 200px;
    margin: 0 auto;
    overflow: hidden;
}

.astleta-player-card .player-card-top .player-picture img {
    width: 100%;
    height: 100%;
    -o-object-fit: cover;
    object-fit: cover;
    position: relative;
    right: -1.5rem;
    bottom: 0;
    cursor: pointer;
}

.astleta-player-card .player-card-top .player-picture .player-extra {
    position: absolute;
    right: 0;
    bottom: -0.5rem;
    overflow: hidden;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    width: 100%;
    height: 2rem;
    padding: 0 1.5rem;
    text-align: right;
    background: none;
}

.astleta-player-card .player-card-top .player-picture .player-extra span {
    margin-left: 0.6rem;
    text-shadow: 2px 2px #333;
}

.astleta-player-card .player-card-bottom {
    position: relative;
}

.astleta-player-card .player-card-bottom .player-info {
    display: block;
    padding: 0.3rem 0;
    color: #e9cc74;
    width: 90%;
    margin: 0 auto;
    height: auto;
    position: relative;
    z-index: 2;
}

.astleta-player-card .player-card-bottom .player-info .player-name {
    width: 100%;
    display: block;
    text-align: center;
    font-size: 1.3rem;
    font-weight: 600;
    text-transform: uppercase;
    border-bottom: 2px solid rgba(233, 204, 116, 0.1);
    padding-bottom: 0.3rem;
    overflow: visible;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}

.astleta-player-card .player-card-bottom .player-info .player-name span {
    display: block;
}

.astleta-player-card .player-card-bottom .player-info .player-features {
    margin: 0.5rem auto;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
}

.astleta-player-card .player-card-bottom .player-info .player-features .player-features-col {
    border-right: 2px solid rgba(233, 204, 116, 0.1);
    padding: 0 2.3rem;
}

.astleta-player-card .player-card-bottom .player-info .player-features .player-features-col span {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    font-size: 1.2rem;
    text-transform: uppercase;
    justify-content: space-between;
    width: 100%;
}

.astleta-player-card .player-card-bottom .player-info .player-features .player-features-col span .player-feature-value {
    margin-right: 0.3rem;
    font-weight: 700;
    font-size: 1.4rem;
    width: 2.5rem;
    text-align: right;
}

.astleta-player-card .player-card-bottom .player-info .player-features .player-features-col span .player-feature-title {
    font-weight: 300;
    font-size: 1.3rem;
    width: 3rem;
    text-align: left;
}

.astleta-player-card .player-card-bottom .player-info .player-features .player-features-col:last-child {
    border: 0;
}

.astleta-player-card .player-card-bottom .player-info .player-logo {
    display: flex;
    justify-content: center;
    margin-top: 0.5rem;
}

.astleta-player-card .player-card-bottom .player-info .player-logo img {
    height: 70px;
    width: auto;
    opacity: 0.7;
    -o-object-fit: cover;
    object-fit: cover;
    object-position: center;
}

.modal-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
</style>
