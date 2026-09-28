<script setup>
import { onMounted, ref } from 'vue';
const currentQuery = window.location.search;
const telegramUserId = ref(null);
const isTelegram = ref(false);
const initDataUnsafe = ref(null);

// function goTo(path) {
//   window.location.href = `${path}${currentQuery}`;
// }

function goTo(path) {
  const chatId = telegramUserId.value;
  const separator = path.includes('?') ? '&' : '?';
  window.location.href = `${path}${separator}telegram_chat_id=${chatId}`;
}

onMounted(() => {


  if (typeof window.Telegram === 'undefined' || !window.Telegram.WebApp) {
    console.warn('❌ Telegram WebApp not detected. Open this inside Telegram.');
    return;
  }

  const tg = window.Telegram.WebApp;
  tg.ready();
  tg.expand();

  isTelegram.value = true;
  initDataUnsafe.value = tg.initDataUnsafe;

  // console.log('✅ Telegram WebApp detected');
  // console.log('📦 initDataUnsafe:', tg.initDataUnsafe);

  const user = tg.initDataUnsafe?.user;
  if (user?.id) {
    telegramUserId.value = user.id;
    console.log('✅ Telegram user ID:', user.id);
  } else {
    console.warn('⚠️ Telegram user ID not found in initDataUnsafe.');
  }
});
</script>

<template>
  <v-app>
    <v-main>
      <v-container
        class="d-flex align-center justify-center fill-height"
        fluid
      >
        <v-card elevation="6" class="pa-6 rounded-xl" max-width="500">
          <h2 class="text-h5 font-weight-bold text-center mb-6">
            Matching
          </h2>

          <div class="d-flex justify-center gap-4 flex-wrap">
            <v-btn
              color="success"
              variant="tonal"
              size="large"
              class="px-6"
              @click="goTo('/api/thirdparty/match-listings')"
            >
              <v-icon left>mdi-home-search-outline</v-icon>
              Find Matching Listing
            </v-btn>

            <v-btn
              color="success"
              variant="tonal"
              size="large"
              class="px-6"
              @click="goTo('/api/thirdparty/search-listings')"
            >
              <v-icon left>mdi-plus-box-outline</v-icon>
              Post My Listing
            </v-btn>
          </div>
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
html,
body,
#app,
.v-application {
  height: 100%;
  width: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.v-application {
  font-family: "Nunito", sans-serif !important;
  background-color: #f9f9f9 !important;
}

.v-btn {
  text-transform: none;
  font-weight: 600;
  font-size: 16px;
}
</style>
