<header>
    <?php
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . '/../admin/error.log'); // Adjust path as needed
        error_reporting(E_ALL);
    ?>
    <div class="header">
        <h1>Renee Kellam</h1>
        <nav>
            <table class="nav-table">
                <tr>
                    <td class="home-nav"><a href="../home"><h2>Home</h2></a></td>
                    <td class="about-nav"><a href="../work-experience"><h2>Work Experience</h2></a></td>
                    <td class="projects-nav"><a href="../projects"><h2>Projects</h2></a></td>
                    <td class="library-nav"><a href="../library"><h2>Library</h2></a></td>
                </tr>
            </table>
            <div class="mobile-nav">
                <button id="menu-toggle">&#9776;</button>
                <div id="dropdown-menu" class="dropdown-content">
                    <a href="../home"><h2>Home</h2></a>
                    <a href="../work-experience"><h2>Work Experience</h2></a>
                    <a href="../projects"><h2>Projects</h2></a>
                    <a href="../library"><h2>Library</h2></a>
                </div>
            </div>
        </nav>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('menu-toggle');
            const menu = document.getElementById('dropdown-menu');
            if (toggle) {
                toggle.onclick = function() {
                    menu.classList.toggle('show');
                };
            }
        });
    </script>
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="//uzw.apx.mybluehost.me/matomo/";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '2']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
</header>