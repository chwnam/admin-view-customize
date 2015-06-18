<p>
	<label for="<?=$this->get_field_id( 'title' )?>">Title:</label>
	<input class="widefat" type="text" id="<?=$this->get_field_id( 'title' )?>" name="<?=$this->get_field_name( 'title' )?>" value="<?=esc_attr( $title )?>" />
</p>
<p>
	<label for="<?=$this->get_field_id( 'movie' )?>">Movie:</label>
	<input class="widefat" type="text" id="<?=$this->get_field_id( 'movie' )?>" name="<?=$this->get_field_name( 'movie' )?>" value="<?=esc_attr( $movie )?>" />
</p>
<p>
	<label for="<?=$this->get_field_id( 'song' )?>">Song:</label>
	<textarea class="widefat" id="<?=$this->get_field_id( 'song' )?>" name="<?=$this->get_field_name( 'song' )?>"><?=esc_attr( $song )?></textarea>
</p>