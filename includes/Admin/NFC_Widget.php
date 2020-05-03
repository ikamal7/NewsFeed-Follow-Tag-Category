<?php
namespace KAMAL\NFC\Admin;

class NFC_Widget extends \WP_Widget {
    public function __construct() {
        $widget_options = [
            'classname'   => 'nfc_wiget',
            'description' => __( 'NFC Wiget to display post category with follow button', 'nfc' ),
        ];
        parent::__construct( 'nfc_wiget', __( 'NFC Category', 'nfc' ), $widget_options );

        add_action( 'wp_ajax_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id'] );
        add_action( 'wp_ajax_nopriv_nfc_ajax_get_id', [$this, 'nfc_ajax_get_id_no'] );
        add_action( 'pre_get_posts', [$this, 'set_posts_cat'] );
    }

    /**
     * @param $query
     */
    public function set_posts_cat( $q ) {
        if ( is_user_logged_in() ) { // First check if we have a logged in user before doing anything
            if ( $q->is_home() // Only targets the main page, home page
                 && $q->is_main_query() // Only targets the main query
            ) {
                // Get the current logged in user
                $current_logged_in_user = wp_get_current_user();
                /*
                 * We will now get the term/category object from the user display_name
                 * You will need to make sure if this corresponds with your term/category
                 * If not, use the correct info to match
                 */
                $term = get_user_meta( $current_logged_in_user->ID, 'post_cat_ids', true );

                if ( $term ) { // Only filter the main query if we actually have a term with the desired name
                    $q->set( 'cat', $term ); // Filter the posts to only show posts from the desired category
                }
            }
        }

    }

    /**
     * @return mixed
     */
    public function nfc_ajax_get_id() {
        global $wpdb;

        if ( !isset( $_POST ) || empty( $_POST ) || !is_user_logged_in() ) {
            echo 'Oops! Try again.';
            exit;
        }
        $current_user = wp_get_current_user();
        $saved_ids    = get_user_meta( $current_user->ID, 'post_cat_ids', true );

        $id_to_add = isset( $_POST["data"] ) ? $_POST["data"] : '';

        if ( in_array( $id_to_add, $saved_ids ) ) {

            if (  ( $key = array_search( $id_to_add, $saved_ids ) ) !== false ) {
                unset( $saved_ids[$key] );
                update_user_meta( $current_user->ID, 'post_cat_ids', $saved_ids );
                echo 'remove from list';
            }

        } else {
            //if list is empty, initialize it as an empty array
            if ( $saved_ids == '' ) {
                $saved_ids = array();
            }

            // push the new ID inside the array
            array_push( $saved_ids, $id_to_add );

            if ( update_user_meta( $current_user->ID, 'post_cat_ids', $saved_ids ) ) {
                echo $id_to_add;
            } else {
                echo 'Failed: Could not update user meta.';
            }
        }

        die();

    }

    public function nfc_ajax_get_id_no() {
        echo "you're not logged in, please login first.";
    }

    function nfc_widget_reg() {
        register_widget( $this );
    }
    /**
     * @param $args
     * @param $instance
     */
    public function widget( $args, $instance ) {

        echo $args['before_widget'];
        if ( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $current_logged_in_user = wp_get_current_user();
        $user_followed_ids      = get_user_meta( $current_logged_in_user->ID, 'post_cat_ids', true );

        $categories = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
            'exclude'    => $user_followed_ids,
        ) );
        $follow_cat_lists = get_categories( array(
            'taxonomy'   => 'category',
            'orderby'    => 'name',
            'parent'     => 0,
            'hide_empty' => 0,
            'include'    => $user_followed_ids,
        ) );

        echo "<ul class='nfc-my-category-list'>";
        //echo $follow_cat_lists;

        echo "</ul>";
        echo "<ul class='nfc-category-list'>";
        if ( is_user_logged_in() ):
            echo "<h2>My list</h2>";
            foreach ( $follow_cat_lists as $follow_cat_list ):
                printf( '<li>%s <a data-cat-id="%s" href="#" class="follow-cat">%s</a></li>', esc_html( $follow_cat_list->name ), esc_attr( $follow_cat_list->term_id ), __( 'Unfollow', 'nfc' ) );

            endforeach;
        endif;
        echo "<h2>Category</h2>";

        foreach ( $categories as $category ):
            printf( '<li>%s <a data-cat-id="%s" href="#" class="follow-cat">%s</a></li>', esc_html( $category->name ), esc_attr( $category->term_id ), __( 'Follow', 'nfc' ) );
        endforeach;
        echo "</ul>";
        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param  mixed  $instance
     * @return void
     */
    public function form( $instance ) {
        $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Category', 'text_domain' );
        ?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' );?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
}

    /**
     * update widget form instance
     *
     * @param  mixed  $new_instance
     * @param  mixed  $old_instance
     * @return void
     */
    public function update( $new_instance, $old_instance ) {
        $instance          = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

        return $instance;
    }
}