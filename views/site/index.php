<?php
use yii\helpers\Url;
use yii\helpers\Html;
$this->title = 'Dungeon RPG - Эпическая браузерная RPG';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .promo-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a0a0a 50%, #0d0d2b 100%);
        font-family: 'Segoe UI', 'Poppins', 'Arial', sans-serif;
    }

    /* Галерея карусель */
    .gallery-section {
        padding: 60px 20px;
    }

    .section-title {
        text-align: center;
        font-size: 42px;
        color: #ffd700;
        margin-bottom: 40px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .carousel-container {
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }

    .carousel-slides {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }

    .carousel-slide {
        min-width: 100%;
        position: relative;
    }

    .carousel-slide img {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }

    .slide-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 18px;
    }

    .carousel-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        padding: 15px 20px;
        cursor: pointer;
        font-size: 24px;
        border-radius: 50%;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .carousel-button:hover {
        background: rgba(255,215,0,0.8);
        color: black;
    }

    .carousel-button.prev {
        left: 20px;
    }

    .carousel-button.next {
        right: 20px;
    }

    .carousel-dots {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dot.active {
        background: #ffd700;
        width: 30px;
        border-radius: 10px;
    }

    /* Описание игры */
    .description-section {
        padding: 60px 20px;
        max-width: 1000px;
        margin: 0 auto;
        text-align: center;
    }

    .game-title {
        font-size: 64px;
        font-weight: bold;
        background: linear-gradient(135deg, #ff6b35, #f7931e, #ffd700);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 30px;
    }

    .game-description {
        background: rgba(0,0,0,0.6);
        border: 1px solid rgba(255,215,0,0.3);
        border-radius: 20px;
        padding: 40px;
        font-size: 18px;
        line-height: 1.8;
        color: #ddd;
    }

    /* Кнопки */
    .buttons-section {
        padding: 40px 20px;
        text-align: center;
    }

    .cta-button {
        display: inline-block;
        padding: 15px 45px;
        font-size: 18px;
        font-weight: bold;
        color: white;
        background: linear-gradient(135deg, #ff6b35, #ff4757);
        border: none;
        border-radius: 50px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(255,107,53,0.5);
        margin: 0 10px;
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 30px rgba(255,107,53,0.8);
        color: white;
    }

    .cta-button.secondary {
        background: linear-gradient(135deg, #2c3e50, #3498db);
    }

    /* Подвал */
    .footer {
        background: rgba(0,0,0,0.9);
        padding: 40px 20px;
        text-align: center;
        color: #888;
    }

    @media (max-width: 768px) {
        .section-title {
            font-size: 32px;
        }
        .game-title {
            font-size: 40px;
        }
        .carousel-slide img {
            height: 300px;
        }
        .carousel-button {
            padding: 10px 15px;
            font-size: 18px;
        }
        .cta-button {
            padding: 12px 25px;
            font-size: 14px;
            margin: 10px 5px;
            display: inline-block;
        }
        .game-description {
            padding: 25px;
            font-size: 16px;
        }
    }
</style>

<div class="promo-container">
    <!-- Галерея скриншотов (карусель) -->
    <div class="gallery-section">
        <h2 class="section-title">📸 Скриншоты игры</h2>
        <div class="carousel-container">
            <button class="carousel-button prev" id="prevBtn">❮</button>
            <div class="carousel-slides" id="carouselSlides">
                <div class="carousel-slide">
                    <img src="/img/screen/4.png" alt="Скриншот 1">
                    <div class="slide-caption">⚔️ Эпические сражения</div>
                </div>
                <div class="carousel-slide">
                    <img src="/img/screen/3.png" alt="Скриншот 2">
                    <div class="slide-caption">🏰 Исследование подземелий</div>
                </div>
                <div class="carousel-slide">
                    <img src="/img/screen/1.png" alt="Скриншот 3">
                    <div class="slide-caption">Обширные возможности</div>
                </div>
            </div>
            <button class="carousel-button next" id="nextBtn">❯</button>
        </div>
        <div class="carousel-dots" id="carouselDots"></div>
    </div>

    <!-- Описание игры (впишите свой текст) -->
    <div class="description-section">
        <div class="game-title">🗡️ DUNGEON RPG 🛡️</div>
        <div class="game-description">
            <!-- ========= ВПИШИТЕ СВОЙ ТЕКСТ ЗДЕСЬ ========= -->
            <p style="margin-bottom: 15px;">
                <strong>Dungeon RPG</strong> — это захватывающая браузерная ролевая игра, 
                которая погрузит вас в мир приключений и эпических сражений.
            </p>
            
            <p style="margin-bottom: 15px;">
                💪 <strong>Прокачка персонажа</strong> — развивайте своего героя, 
                открывайте новые способности и становитесь сильнее!
            </p>
            
            <p style="margin-bottom: 15px;">
                ⚔️ <strong>PvP Арена</strong> — сражайтесь с другими игроками 
                в захватывающих дуэлях и командных битвах.
            </p>
            
            <p style="margin-bottom: 15px;">
                🏰 <strong>Подземелья</strong> — исследуйте опасные подземелья, 
                сражайтесь с боссами и собирайте ценные трофеи.
            </p>
            
            <p style="margin-bottom: 15px;">
                👥 <strong>Кланы и сообщество</strong> — объединяйтесь с друзьями, 
                создавайте кланы и вместе покоряйте игровой мир!
            </p>
            
            <p>
                🎮 <strong>Присоединяйся сейчас и начни свое приключение!</strong>
            </p>
            <!-- ========= КОНЕЦ ВАШЕГО ТЕКСТА ========= -->
        </div>
    </div>




</div>

<script>
    // Карусель
    const slides = document.querySelectorAll('.carousel-slide');
    const slidesContainer = document.getElementById('carouselSlides');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dotsContainer = document.getElementById('carouselDots');
    
    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Создаем точки
    for (let i = 0; i < totalSlides; i++) {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }
    
    const dots = document.querySelectorAll('.dot');
    
    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        currentIndex = index;
        slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
    }
    
    function nextSlide() {
        goToSlide(currentIndex + 1);
    }
    
    function prevSlide() {
        goToSlide(currentIndex - 1);
    }
    
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);
    
    // Автопрокрутка каждые 5 секунд
    let autoSlide = setInterval(nextSlide, 5000);
    
    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', () => {
        clearInterval(autoSlide);
    });
    
    carouselContainer.addEventListener('mouseleave', () => {
        autoSlide = setInterval(nextSlide, 5000);
    });
</script>