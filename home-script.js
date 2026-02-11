// ================================
// Smooth scroll for navigation links (SAFE)
// ================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');

        // Ignore plain "#"
        if (!href || href === '#') return;

        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ================================
// Navbar scroll effect
// ================================
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.05)';
    }
});

// ================================
// Feature card redirects
// ================================
console.log("home-script loaded ✅");

// Feature card redirects (guaranteed)
document.querySelectorAll('.feature-card').forEach(card => {
  card.style.cursor = 'pointer';

  card.addEventListener('click', () => {
    const link = card.dataset.link;
    console.log("Card clicked ->", link);
    if (link) window.location.href = link;
  });
});


// ================================
// Animate elements on scroll
// ================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe feature cards
document.querySelectorAll('.feature-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
});

// Observe stat cards
document.querySelectorAll('.stat-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `all 0.6s ease ${index * 0.15}s`;
    observer.observe(card);
});

// ================================
// Counter animation for stats
// ================================
const animateCounter = (element, target) => {
    let current = 0;
    const increment = target / 100;
    const duration = 2000;
    const stepTime = duration / 100;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
};

// Trigger counter animation when stat cards are visible
const statObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
            entry.target.classList.add('counted');

            const numberElement = entry.target.querySelector('.stat-number');
            const originalText = numberElement.textContent;
            const target = parseInt(originalText.replace(/[^0-9]/g, ''), 10);

            numberElement.textContent = '0';

            setTimeout(() => {
                animateCounter(numberElement, target);

                setTimeout(() => {
                    if (originalText.includes('%')) {
                        numberElement.textContent = target + '%';
                    } else if (originalText.includes('+')) {
                        numberElement.textContent = target + '+';
                    } else {
                        numberElement.textContent = target;
                    }
                }, 2000);
            }, 200);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-card').forEach(card => {
    statObserver.observe(card);
});

// ================================
// Page load
// ================================
window.addEventListener('load', () => {
    console.log('Coffee Shop Management System loaded!');
});
