(() => {
    const canUsePointer = window.matchMedia('(pointer: fine)').matches;
    const cursor = document.createElement('span');
    const glow = document.createElement('span');
    cursor.className = 'effects-cursor';
    glow.className = 'effects-cursor-glow';
    document.body.append(cursor, glow);

    document.addEventListener('pointermove', (event) => {
        glow.style.left = `${event.clientX}px`;
        glow.style.top = `${event.clientY}px`;

        if (canUsePointer) {
            cursor.style.left = `${event.clientX}px`;
            cursor.style.top = `${event.clientY}px`;
        }
    });

    document.addEventListener('pointerover', (event) => {
        if (event.target.closest('a, button, input, textarea, select, [role="button"]')) {
            cursor.classList.add('is-hovering');
        }
    });

    document.addEventListener('pointerout', (event) => {
        if (event.target.closest('a, button, input, textarea, select, [role="button"]')) {
            cursor.classList.remove('is-hovering');
        }
    });

    document.addEventListener('pointerdown', (event) => {
        glow.style.left = `${event.clientX}px`;
        glow.style.top = `${event.clientY}px`;
        glow.classList.add('is-touching');
        window.clearTimeout(glow.touchTimeout);
        glow.touchTimeout = window.setTimeout(() => glow.classList.remove('is-touching'), 260);

        for (let index = 0; index < 10; index += 1) {
            const spark = document.createElement('span');
            const angle = (Math.PI * 2 * index) / 10 + Math.random() * .35;
            const distance = 26 + Math.random() * 30;
            spark.className = 'effects-spark';
            spark.style.left = `${event.clientX}px`;
            spark.style.top = `${event.clientY}px`;
            spark.style.setProperty('--spark-x', `${Math.cos(angle) * distance}px`);
            spark.style.setProperty('--spark-y', `${Math.sin(angle) * distance}px`);
            document.body.append(spark);
            spark.addEventListener('animationend', () => spark.remove(), { once: true });
        }
    });
})();
