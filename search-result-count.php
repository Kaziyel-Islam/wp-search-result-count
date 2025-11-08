<?php 
add_action('wp_footer', function() {
    // Run only on frontend search result pages (not admin, not AJAX)
    if (!is_admin() && is_search()) {
        global $wp_query;
        $search_query = get_search_query();
        $result_count = $wp_query->found_posts;

        // Build the message HTML safely
        $message = '';
        if ($search_query) {
            if ($result_count > 0) {
                $message = sprintf(
                    '<div class="search-count-message">“%s” appears %d %s in your search results.</div>',
                    esc_html($search_query),
                    $result_count,
                    _n('time', 'times', $result_count, 'text-domain')
                );
            } else {
                $message = sprintf(
                    '<div class="search-count-message">“%s” does not appear in any results.</div>',
                    esc_html($search_query)
                );
            }
        }

        // Output JavaScript to inject message as the first child of your target div
        if (!empty($message)) : ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const targetDiv = document.querySelector('.has-content-section.documents-list-section.st2.h-search');
                if (targetDiv) {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = <?php echo json_encode($message); ?>;
                    targetDiv.prepend(wrapper.firstChild);
                }
            });
            </script>
        <?php endif;
    }
});



// if have custom query for search 

add_action('wp_footer', function() {
    // Only run on frontend
    if (is_admin() || wp_doing_ajax()) return;

    // Check if any search field is used
    $s   = !empty($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $m   = !empty($_GET['m']) ? sanitize_text_field($_GET['m']) : '';
    $y   = !empty($_GET['y']) ? sanitize_text_field($_GET['y']) : '';
    $vol = !empty($_GET['vol']) ? sanitize_text_field($_GET['vol']) : '';

    if ($s || $m || $y || $vol) {

        // Make sure the theme function exists
        if (function_exists('hasvit_get_sContent')) {
            $custom_query = hasvit_get_sContent();
            $result_count = isset($custom_query->found_posts) ? (int)$custom_query->found_posts : 0;
        } else {
            $result_count = 0;
        }

        // Build search summary
        $parts = [];
        if ($s)   $parts[] = 'Keyword: “' . esc_html($s) . '”';
        if ($m)   $parts[] = 'Month: ' . esc_html($m);
        if ($y)   $parts[] = 'Year: ' . esc_html($y);
        if ($vol) $parts[] = 'Volume: ' . esc_html($vol);
        $search_summary = implode(' | ', $parts);

        // Build message
        if ($result_count > 0) {
            $message = sprintf(
                '<div class="search-count-message">%s — %d %s found.</div>',
                $search_summary,
                $result_count,
                _n('result', 'results', $result_count, 'hasvit')
            );
        } else {
            $message = sprintf(
                '<div class="search-count-message"> <strong> %s </strong> — No results found.</div>',
                $search_summary
            );
        }

        // Output via JS
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const targetDiv = document.querySelector('.has-content-section.documents-list-section.st2.h-search');
            if (targetDiv) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = <?php echo json_encode($message); ?>;
                targetDiv.prepend(wrapper.firstChild);
            }
        });
        </script>
        <?php
    }
});




