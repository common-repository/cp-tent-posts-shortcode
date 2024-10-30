<?php
class CP_Tent_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct('cp_tent_widget','CP Tent Widget',array( 'description' => __( 'A Tent Widget', 'text_domain' ),));
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$taddress = apply_filters( 'widget_taddress', $instance['taddress'] );
		$tmentions = apply_filters( 'widget_tmentions', $instance['tmentions'] );
		$tlimit = apply_filters( 'widget_tlimit', $instance['tlimit'] );
		if(is_int($tlimit) || ctype_digit($tlimit)):
			$tpwlimit = $tlimit;
		else:
			$tpwlimit = 4;
		endif;
		$targs = array('limit'=>$tpwlimit,'must_mention'=>$tmentions);
		$cpwtarget = cp_tentsc_discovery($taddress);
		$cpwposts = retrieve_public_tent_posts($cpwtarget['server'],$targs);
		//print_r($cpwposts);
		$tentwcounter=0;
		echo '<span class="cptentwbasic"><a class="cptentlink" href="' . $cpwtarget['avatar_url'] . '"><img src="' . $cpwtarget['avatar_url'] . '" /></a> <a class="cptentlink" href="' . $taddress . '">' . $cpwtarget['name'] . '</a>&emsp;&mdash;&emsp;Tent feed</span><br />
		';
		echo '<ul class="cptentwidget">
		';
		foreach($cpwposts as $key=>$tentpost) {


			echo '<li><a class="cptentlink" href="' . $taddress . '/posts/' . $key . '">&crarr;</a> ' . $tentpost['text'] . '</li>';
			$tentwcounter++;
		}
		echo '</ul>
		';
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['taddress'] = strip_tags( $new_instance['taddress'] );
		$instance['tmentions'] = $new_instance['tmentions'];
		$instance['tlimit'] = $new_instance['tlimit'];
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'taddress' ] ) ) {
			$taddress = $instance[ 'taddress' ];
		}
		else {
			$taddress = __( 'https://tent.tent.is', 'text_domain' );
		}

		$tmentions = $instance[ 'tmentions' ];
		if(isset($instance['tlimit'])):
			$tlimit = $instance[ 'tlimit' ];
		else:
			$tlimit = 4;
		endif;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'taddress' ); ?>"><?php _e( 'Tent Address:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'taddress' ); ?>" name="<?php echo $this->get_field_name( 'taddress' ); ?>" type="text" value="<?php echo esc_attr( $taddress ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'tmentions' ); ?>"><?php _e( 'Status must mention:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'tmentions' ); ?>" name="<?php echo $this->get_field_name( 'tmentions' ); ?>" type="text" value="<?php echo esc_attr( $tmentions ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'tlimit' ); ?>"><?php _e( 'Limit posts:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'tlimit' ); ?>" name="<?php echo $this->get_field_name( 'tlimit' ); ?>" type="number" step="1" min="1" max="200" value="<?php echo esc_attr( $tlimit ); ?>" />
		</p>
		<?php 
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget( "cp_tent_widget" );' ) );

?>