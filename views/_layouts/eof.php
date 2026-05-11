        <?php if (!empty($page['layout'])): ?>
                    </div><!-- /dashboard-content -->
                </div><!-- /dashboard-main -->
            </div><!-- /dashboard-layout -->
        <?php endif; ?>

        <?php if (!empty($page['footer'])) { ?>

        <!-- FOOTER -->
        <?php require(LAYOUT_PATH . $page['footer'] . '/footer.php'); ?>

        <?php } ?>

        <!-- JS POSTLOAD FILES -->
        <?php
        if (isset($page['jspostloadlist']) && is_array($page['jspostloadlist'])) {
            foreach ($page['jspostloadlist'] as $jsFile) {
                echo '<script src="/assets/js/' . htmlspecialchars($jsFile) . '.js"></script>' . "\n";
            }
        }
        ?>
    </body>
</html>