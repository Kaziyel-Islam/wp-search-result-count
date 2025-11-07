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
