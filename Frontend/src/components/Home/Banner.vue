<template>
  <div class="banner">
    <div class="banner-container">
      <!-- Slides -->
      <transition name="slide" mode="out-in">
        <div :key="currentSlide" class="slide">
          <div class="slide-content">
            <span class="slide-emoji">{{ slides[currentSlide].emoji }}</span>
            <h2>{{ slides[currentSlide].title }}</h2>
            <p>{{ slides[currentSlide].description }}</p>
            <button class="btn-order" @click="goToOrder">
              {{ slides[currentSlide].buttonText }}
            </button>
          </div>
        </div>
      </transition>

      <!-- Indicators -->
      <div class="indicators">
        <button
          v-for="(slide, index) in slides"
          :key="index"
          @click="goToSlide(index)"
          :class="['indicator', { active: currentSlide === index }]"
        ></button>
      </div>

      <!-- Navigation Arrows -->
      <button class="nav-btn prev" @click="prevSlide">‹</button>
      <button class="nav-btn next" @click="nextSlide">›</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

interface Slide {
  emoji: string
  title: string
  description: string
  buttonText: string
  background: string
}

const slides: Slide[] = [
  {
    emoji: '🍜',
    title: 'Phở Bò Đặc Biệt - Giảm 30%',
    description: 'Phở bò truyền thống với thịt tái mềm, nước dùng thơm ngon',
    buttonText: 'Đặt ngay',
    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
  },
  {
    emoji: '🍖',
    title: 'Bún Chả Hà Nội - Món Mới',
    description: 'Chả nướng thơm lừng, bún tươi ngon, nước mắm chuẩn vị',
    buttonText: 'Thử ngay',
    background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
  },
  {
    emoji: '🍗',
    title: 'Combo Gà Rán - Ưu Đãi Hot',
    description: 'Gà rán giòn tan + Khoai tây + Pepsi chỉ 99k',
    buttonText: 'Mua ngay',
    background: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
  },
  {
    emoji: '🍕',
    title: 'Pizza Hải Sản - Freeship',
    description: 'Pizza size L với topping hải sản tươi ngon, freeship 0đ',
    buttonText: 'Đặt món',
    background: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
  }
]

const currentSlide = ref(0)
let autoPlayInterval: number | null = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
}

const goToSlide = (index: number) => {
  currentSlide.value = index
}

const goToOrder = () => {
  router.push('/order')
}

const startAutoPlay = () => {
  autoPlayInterval = window.setInterval(() => {
    nextSlide()
  }, 6000)
}

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

onMounted(() => {
  startAutoPlay()
})

onUnmounted(() => {
  stopAutoPlay()
})
</script>

<style scoped>
.banner {
  position: relative;
  width: 100%;
  height: 450px;
  overflow: hidden;
  border-radius: 80px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.banner-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.slide {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: v-bind('slides[currentSlide].background');
  padding: 40px;
}

.slide-content {
  text-align: center;
  color: white;
  max-width: 700px;
  animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-emoji {
  font-size: 6rem;
  display: block;
  margin-bottom: 20px;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
  animation: bounce 2s ease infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.slide-content h2 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 15px;
  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.slide-content p {
  font-size: 1.2rem;
  margin-bottom: 30px;
  opacity: 0.95;
  text-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
}

.btn-order {
  padding: 16px 40px;
  background: white;
  color: #667eea;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.btn-order:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
}

/* Navigation Arrows */
.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
  border: none;
  border-radius: 50%;
  font-size: 2rem;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
}

.nav-btn:hover {
  background: rgba(255, 255, 255, 0.5);
  transform: translateY(-50%) scale(1.1);
}

.nav-btn.prev {
  left: 20px;
}

.nav-btn.next {
  right: 20px;
}

/* Indicators */
.indicators {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 10;
}

.indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  border: 2px solid white;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
}

.indicator:hover {
  background: rgba(255, 255, 255, 0.6);
  transform: scale(1.2);
}

.indicator.active {
  width: 30px;
  border-radius: 6px;
  background: white;
}

/* Slide Transitions */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.5s ease;
}

.slide-enter-from {
  opacity: 0;
  transform: translateX(100px);
}

.slide-leave-to {
  opacity: 0;
  transform: translateX(-100px);
}

/* Responsive */
@media (max-width: 768px) {
  .banner {
    height: 350px;
    border-radius: 0 0 20px 20px;
  }

  .slide-emoji {
    font-size: 4rem;
  }

  .slide-content h2 {
    font-size: 1.8rem;
  }

  .slide-content p {
    font-size: 1rem;
  }

  .nav-btn {
    width: 40px;
    height: 40px;
    font-size: 1.5rem;
  }

  .nav-btn.prev {
    left: 10px;
  }

  .nav-btn.next {
    right: 10px;
  }
}
</style>
