/**
 * Main
 */

'use strict';

let menu, animate;

(function () {
  // Button & Pagination Waves effect
  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(
      ".btn[class*='btn-']:not(.position-relative):not([class*='btn-outline-']):not([class*='btn-label-'])",
      ['waves-light']
    );
    Waves.attach("[class*='btn-outline-']:not(.position-relative)");
    Waves.attach('.pagination .page-item .page-link');
    Waves.attach('.dropdown-menu .dropdown-item');
    Waves.attach('.light-style .list-group .list-group-item-action');
    Waves.attach('.dark-style .list-group .list-group-item-action', ['waves-light']);
    Waves.attach('.nav-tabs:not(.nav-tabs-widget) .nav-item .nav-link');
    Waves.attach('.nav-pills .nav-item .nav-link', ['waves-light']);
    Waves.attach('.menu-vertical .menu-item .menu-link.menu-toggle');
  }

  // Window scroll function for navbar
  function onScroll() {
    var layoutPage = document.querySelector('.layout-page');
    if (layoutPage) {
      if (window.pageYOffset > 0) {
        layoutPage.classList.add('window-scrolled');
      } else {
        layoutPage.classList.remove('window-scrolled');
      }
    }
  }
  // On load time out
  setTimeout(() => {
    onScroll();
  }, 200);

  // On window scroll
  window.onscroll = function () {
    onScroll();
  };

  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: 'vertical',
      closeChildren: false
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  // Initialize menu togglers and bind click on each
  let menuToggler = document.querySelectorAll('.layout-menu-toggle');
  menuToggler.forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      window.Helpers.toggleCollapsed();
    });
  });

  // Display menu toggle (layout-menu-toggle) on hover with delay
  let delay = function (elem, callback) {
    let timeout = null;
    elem.onmouseenter = function () {
      // Set timeout to be a timer which will invoke callback after 300ms (not for small screen)
      if (!Helpers.isSmallScreen()) {
        timeout = setTimeout(callback, 300);
      } else {
        timeout = setTimeout(callback, 0);
      }
    };

    elem.onmouseleave = function () {
      // Clear any timers set to timeout
      document.querySelector('.layout-menu-toggle').classList.remove('d-block');
      clearTimeout(timeout);
    };
  };
  if (document.getElementById('layout-menu')) {
    delay(document.getElementById('layout-menu'), function () {
      // not for small screen
      if (!Helpers.isSmallScreen()) {
        document.querySelector('.layout-menu-toggle').classList.add('d-block');
      }
    });
  }

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Nav tabs animation
  window.Helpers.navTabsAnimation();

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (window.Helpers.isSmallScreen()) {
    return;
  }

  // If current layout is vertical and current window screen is > small

  // Auto update menu collapsed/expanded based on the themeConfig
  window.Helpers.setCollapsed(true, false);
})();

// $(document).on('change','#AttandanceActivity',function(e){
//     var this_ = $(this)
//     var selectedValue = $(this).is(':checked');
//     $(this).prop('checked',selectedValue ? false : true )
//     $.ajax({
//         type: "POST",
//         url: BASE_URL + "/SRI/update-activity",
//         data: {
//             activity : selectedValue == true ? 1 : 0,
//             _token   : $('meta[name="csrf_token"]').attr('content')
//         },
//         dataType: "json",
//         success: function (response) {
//             if(response.status){
//                 $("#AttandanceActivity").prop('checked',selectedValue == true ? true : false)
//             }
//         }
//     });

// })



// search

// let searchTimer = null;

// document.getElementById("globalSearch").addEventListener("keyup", function () {
//     const q = this.value.trim();
//     if (!q) {
//         document.getElementById("globalSearchResults").innerHTML = '';
//         return;
//     }

//     clearTimeout(searchTimer);
//     searchTimer = setTimeout(() => {
//         fetch(`/search/global?q=${encodeURIComponent(q)}`)
//             .then(res => res.json())
//             .then(data => renderSearchResults(data));
//     }, 300);
// });

// function renderSearchResults(payload) {
//     const wrap = document.getElementById("globalSearchResults");
//     wrap.innerHTML = "";

//     const data = payload;
//     console.log(payload);

//     ['customers', 'leads', 'projects'].forEach(type => {
//         data[type].forEach(item => {
//             const div = document.createElement("div");
//             div.className = "search-item";

//             div.innerHTML = `<strong>${item.label}</strong> <small>(${type})</small>`;
//             div.onclick = () => selectSearchItem(item);
//             wrap.appendChild(div);
//         });
//     });
// }


// function selectSearchItem(item) {
//     console.log("Selected:", item);

//     if (item.type === 'customer') {
//         window.location.href = `/customers/${item.id}/edit`;
//     }
//     if (item.type === 'lead') {
//         window.location.href = `/marketing/${item.id}/view`;
//     }
//     if (item.type === 'project') {
//         window.location.href = `/projects/${item.id}/view`;
//     }
// }

let timer = null;

function globalSearchInit() {
    const input = document.getElementById("globalSearchInput");
    const dropdown = document.getElementById("globalSearchResults");

    input.addEventListener("input", function () {
        clearTimeout(timer);
        dropdown.innerHTML = "";
        dropdown.classList.remove("show");

        const q = this.value.trim();
        if (!q.length) return;

        timer = setTimeout(() => searchRequest(q), 300);
    });

    async function searchRequest(q) {
        const res = await fetch(`/search/global?q=${encodeURIComponent(q)}`);
        const json = await res.json();

        renderDropdown(json.results);
    }

    function renderDropdown(groups) {
        dropdown.innerHTML = "";
        dropdown.classList.add("show");

        Object.entries(groups).forEach(([type, items]) => {
            if (!items.length) return;

            dropdown.innerHTML += `<div class="search-header">${type.toUpperCase()}</div>`;

            items.forEach(item => {
                dropdown.innerHTML += `
                    <div class="search-item" onclick="selectGlobalItem('${item.type}', ${item.id})">
                        <div>
                            <div>${item.label}</div>
                            <small class="text-muted">${item.sub ?? ''}</small>
                        </div>
                        <span class="badge bg-primary">${item.badge}</span>
                    </div>
                `;
            });
        });
    }
}

window.selectGlobalItem = function (type, id) {
    if (type === "customer") {
        window.location.href = `/customers/${id}/edit`;
    } else if (type === "lead") {
        window.location.href = `/marketing/leads/${id}/view`;
    } else if (type === "project") {
        window.location.href = `/projects/${id}/edit`;
    }
};
globalSearchInit();
document.querySelectorAll('#layout-menu .menu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function () {
        const submenu = this.nextElementSibling;
        submenu.style.display = submenu.style.display === "block" ? "none" : "block";
    });
});
document.addEventListener('DOMContentLoaded', function () {

    const menu = document.getElementById('layout-menu');
    const toggle = document.getElementById('mobile-menu-toggle');
    const overlay = document.getElementById('menu-overlay');

    // Open mobile menu
    toggle.addEventListener('click', () => {
        menu.classList.add('menu-open');
        overlay.classList.add('active');
    });

    // Close when clicking overlay
    overlay.addEventListener('click', () => {
        menu.classList.remove('menu-open');
        overlay.classList.remove('active');
    });

    // Auto close on window resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            menu.classList.remove('menu-open');
            overlay.classList.remove('active');
        }
    });
});
