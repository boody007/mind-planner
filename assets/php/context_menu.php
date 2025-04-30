<div class="context-menu" id="contextMenu">
    <div class="context-menu__item" onclick="alert('Option 1 selected!')">Option 1</div>
    <div class="context-menu__item" onclick="alert('Option 2 selected!')">Option 2</div>
    <div class="context-menu__item disabled">Disabled Option</div>
    <div class="context-menu__item" onclick="alert('Option 3 selected!')">Option 3</div>
</div>

<script>
        const contextMenu = document.getElementById('contextMenu');

    document.addEventListener('contextmenu', (event) => {
        event.preventDefault();
        const { clientX: mouseX, clientY: mouseY } = event;

        // Adjust position to avoid going out of bounds
        const { innerWidth: windowWidth, innerHeight: windowHeight } = window;
        const menuWidth = contextMenu.offsetWidth;
        const menuHeight = contextMenu.offsetHeight;

        const positionX = mouseX + menuWidth > windowWidth ? windowWidth - menuWidth : mouseX;
        const positionY = mouseY + menuHeight > windowHeight ? windowHeight - menuHeight : mouseY;

        contextMenu.style.top = `${positionY}px`;
        contextMenu.style.left = `${positionX}px`;
        contextMenu.style.display = 'block';
    });

    document.addEventListener('click', () => {
        contextMenu.style.display = 'none';
    });

</script>