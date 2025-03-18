document.addEventListener('DOMContentLoaded', function() {
    // Find the menu more flexibly
    function findMainMenu() {
        // Try multiple potential selectors
        const menuSelectors = [
            '.main-header-menu',  // Astra default
            '#primary-menu',      // Common WordPress menu ID
            '.menu',              // Generic menu class
            'nav ul',             // Navigation unordered list
            '.wp-block-navigation__container'  // Gutenberg navigation block
        ];

        for (let selector of menuSelectors) {
            const menu = document.querySelector(selector);
            if (menu) return menu;
        }

        console.error('Could not find main menu. Menu clone failed.');
        return null;
    }

    // Create hamburger icon
    const hamburgerContainer = document.createElement('div');
    hamburgerContainer.className = 'cd-hamburger-icon';
    hamburgerContainer.innerHTML = `
        <span></span>
        <span></span>
        <span></span>
    `;

    // Create menu overlay
    const menuOverlay = document.createElement('div');
    menuOverlay.className = 'cd-menu-overlay';

    // Create off-canvas menu container
    const offCanvasMenu = document.createElement('div');
    offCanvasMenu.className = 'cd-off-canvas-menu';

    // Clone main navigation menu
    const mainMenu = findMainMenu();
    if (mainMenu) {
        const clonedMenu = mainMenu.cloneNode(true);
        
        // Remove any existing IDs to prevent duplicate IDs
        clonedMenu.removeAttribute('id');
        
        // Clean up classes that might interfere with styling
        clonedMenu.className = 'menu cd-cloned-menu';
        
        offCanvasMenu.appendChild(clonedMenu);
    }

    // Append elements to body
    document.body.appendChild(hamburgerContainer);
    document.body.appendChild(menuOverlay);
    document.body.appendChild(offCanvasMenu);

    // Toggle menu function
    function toggleMenu() {
        hamburgerContainer.classList.toggle('open');
        offCanvasMenu.classList.toggle('open');
        menuOverlay.classList.toggle('open');
    }

    // Event listeners
    hamburgerContainer.addEventListener('click', toggleMenu);
    menuOverlay.addEventListener('click', toggleMenu);

    // Close menu when clicking menu items
    offCanvasMenu.addEventListener('click', function(e) {
        const clickedLink = e.target.closest('a');
        if (clickedLink) {
            toggleMenu();
        }
    });

    // Handle submenus
    const subMenuParents = offCanvasMenu.querySelectorAll('.menu-item-has-children, .sub-menu-item');
    subMenuParents.forEach(parent => {
        const link = parent.querySelector('a');
        const subMenu = parent.querySelector('.sub-menu');
        
        if (link && subMenu) {
            // Create dropdown toggle
            const dropdownToggle = document.createElement('span');
            dropdownToggle.className = 'submenu-toggle';
            dropdownToggle.innerHTML = '▼';
            dropdownToggle.style.marginLeft = '10px';
            dropdownToggle.style.cursor = 'pointer';
            
            link.appendChild(dropdownToggle);
            
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                subMenu.classList.toggle('submenu-open');
                dropdownToggle.textContent = subMenu.classList.contains('submenu-open') ? '▲' : '▼';
            });
        }
    });
});
