<?php
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	/*
		Plugin Name: DT World Clock
		Plugin URI: http://www.deligence.com 
		Author: Deligence Technologies
		Author URI: http://www.deligence.com 
		Version: 1.8
		Description: its a plugin to show world clocks
	*/
	
	/* Copyright 2015-16 Deligence Technologies (email : sanjay@deligence.com)
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License as
	published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	108 The WordPress Anthology This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public
	License along with this program; if not, write to the Free Software
	Foundation, Inc.,
	*/	
	global $date_formats;
	$date_formats=array("YYYY-MM-DD"=>"YYYY-MM-DD","DD-MM-YYYY"=>"DD-MM-YYYY","MM-DD-YYYY"=>"MM-DD-YYYY","MMM-DD-YYYY"=>"Jul-11-2002","DD-MMM-YYYY"=>"11-Jul-2002","MMMM-DD-YYYY"=>"July-11-2002","DD-MMMM-YYYY"=>"11-July-2002");
	
	/* to register plugin */
	function dt_register()
	{
		
		 //code here
	}
	register_activation_hook( __FILE__ ,'dt_register');
	
	function dt_uninstall()
	{
		delete_option('dt_wid_title_color');
		delete_option('dt_clock_color');
		delete_option('dt_format');
		delete_option('dt_layout');		
		delete_option('dt_align');
		delete_option('dt_sec');
		delete_option('dt_zeros');
		delete_option('dt_date');
		delete_option('dt_date_separator');
		delete_option('dt_week_day');
		/*------------------Advance------------------------*/
		for($i=1;$i<=4;$i++)
		{
			delete_option('dt_clock'.$i.'_show');
			delete_option('dt_clock'.$i.'_timezone');
			delete_option('dt_clock'.$i.'_text');
		}
	}
	
	register_uninstall_hook( __FILE__ ,'dt_uninstall');
	
	/* to create plugin menu in admin area */
	function my_scripts() {
	wp_enqueue_script( 'tabcontent-js', plugins_url( 'dt-world-clock/js/tabcontent.js' , dirname(__FILE__) ) );
	wp_enqueue_style('tabcontent-css', plugins_url( 'dt-world-clock/css/tabcontent.css' , dirname(__FILE__) ) );
	}		
	
	function dt_create_menu()
	{
		$menu=add_menu_page( 'DT-World-Clock', 'DTW Clock', 'manage_options', 'dt-world-clock/settings.php', '', plugins_url( 'dt-world-clock/dtw_clock.png' ), 6 );	
		add_action('admin_enqueue_scripts', 'my_scripts');	
		add_action( 'admin_init', 'dt_register_settings' );			
	}
	
	add_action('admin_menu','dt_create_menu');
	
		
	/* to save settings of our plugin */
	function dt_register_settings()
	{
		register_setting( 'dt_settings_group', 'dt_wid_title_color' );
		register_setting( 'dt_settings_group', 'dt_clock_color' );
		register_setting( 'dt_settings_group', 'dt_format' );
		register_setting( 'dt_settings_group', 'dt_layout' );		
		register_setting( 'dt_settings_group', 'dt_align'  );
		register_setting( 'dt_settings_group', 'dt_sec'    );
		register_setting( 'dt_settings_group', 'dt_zeros'  );
		register_setting( 'dt_settings_group', 'dt_date'   );
		register_setting( 'dt_settings_group', 'dt_date_separator');
		register_setting( 'dt_settings_group', 'dt_week_day');
		/*------------------Advance------------------------*/
		for($i=1;$i<=4;$i++)
		{
			register_setting( 'dt_settings_group', 'dt_clock'.$i.'_show');
			register_setting( 'dt_settings_group', 'dt_clock'.$i.'_timezone');
			register_setting( 'dt_settings_group', 'dt_clock'.$i.'_text');
		}		
	}
	
	function register_world_clock_js() {
		
   	 	 wp_register_script('moment_js', plugins_url( 'dt-world-clock/js/moment.js' , dirname(__FILE__) ));
   		 wp_register_script('moment-timezone-all-years_js', plugins_url( 'dt-world-clock/js/moment-timezone-all-years.js' , dirname(__FILE__) ));
		 
		 
	}
	
	// Use wp_enqueue_scripts hook
	add_action('init', 'register_world_clock_js',0);
	
	/* widget */
	class Dtw_Clock_Widget extends WP_Widget
	{		
	   
		//* Widget setup */
		function __construct()
		{
			// actual widget code that contains the function logic
			$widget_ops = array('classname' => 'Dtw_Clock_Widget', 'description' =>__( 'Show DT World Clock on FrontEnd') );
			// The actual widget code goes here
			parent::__construct( false, 'DT World Clock', $widget_ops );
		}
		
		function widget($args, $instance) {
			wp_enqueue_script('moment_js');
        	wp_enqueue_script('moment-timezone-all-years_js');		
				
			// display the widget on website
			//get widget arguments
			$format=get_option('dt_format');
			$layout=get_option('dt_layout');			
			$align=get_option('dt_align');
			$sec=get_option( 'dt_sec' );
			$zeros=get_option( 'dt_zeros' );
			$date=get_option( 'dt_date' );
			$separator=get_option( 'dt_date_separator' )=="space" ? " " : get_option( 'dt_date_separator' );			
			$clock1_show=get_option( 'dt_clock1_show' );
			$clock2_show=get_option( 'dt_clock2_show' );
			$clock3_show=get_option( 'dt_clock3_show' );
			$clock4_show=get_option( 'dt_clock4_show' );
			$weekday=get_option( 'dt_week_day' );
			
			global $date_formats;
			$title = apply_filters('widget_title', $instance['title']);		
			echo $args['before_widget'];  
			
			$dt_clock_color=get_option( 'dt_clock_color' );  
			if(!$dt_clock_color)  {$dt_clock_color='#000';}   
			
			$dt_wid_title_color=get_option( 'dt_wid_title_color' );  
			if(!$dt_wid_title_color)  {$dt_wid_title_color='#000';}              
            ?>					
						
			
			<style>
				.dt_clocks{
					list-style:none;
					margin:0;
					color:<?php echo $dt_clock_color; ?>;
					
				}
				.dt_clocks li
				{
					/* vertical */
					<?php if($layout==2){?>display:inline-block;<?php }?>	
					padding:2px;				
				}
				.digits
				{
					font-size:18px;
				}
				.wid_title_color{
					color:<?php echo $dt_wid_title_color; ?>;
				}
			</style>
            
            
           
            <script>
				var moment='';
				function DisplayTime(span_id,timezone)
				{
					if(moment !== null && typeof moment !== 'undefined' && moment !== undefined && moment != '' && moment.tz){
							id=span_id.split("_");
							var now=moment.tz(timezone).format("<?php if($weekday){ ?>dddd, <?php } ?><?php echo $date;?>");
							var ulzero='y';
							var weekday="<?php echo $weekday; ?>";
							//alert(now);
							var time=null;
							<?php if($format==1){?>
								time=moment.tz(timezone).format("HH:mm:ss");
								<?php if(!$sec){?>
									time=moment.tz(timezone).format("HH:mm");
								<?php } ?>
								
								<?php if($zeros==2){?>
									time=moment.tz(timezone).format("H:mm:ss");
									<?php if(!$sec){?>
									time=moment.tz(timezone).format("H:mm");
									<?php } ?>
								<?php } ?>
								<?php if($zeros==3){?>
									time=moment.tz(timezone).format("H:m:s");
									ulzero='n';
									<?php if(!$sec){?>
									time=moment.tz(timezone).format("H:m");
									<?php } ?>
								<?php } ?>
								
							<?php }else{?>
								time=moment.tz(timezone).format("hh:mm:ss A");
								<?php if(!$sec){?>
									time=moment.tz(timezone).format("hh:mm A");
									<?php } ?>
								<?php if($zeros==2){?>
									time=moment.tz(timezone).format("h:mm:ss A");
									<?php if(!$sec){?>
									time=moment.tz(timezone).format("h:mm A");
									<?php } ?>
								<?php } ?>
								<?php if($zeros==3){?>
									time=moment.tz(timezone).format("h:m:s A");
									ulzero='n';
									<?php if(!$sec){?>
									time=moment.tz(timezone).format("h:m A");
									<?php } ?>
								<?php } ?>
							<?php }?>
							
							//dnt=now.split(new RegExp('[-+T]','g'));
							
							if(ulzero == 'n'){
								var weekdayStr='';
								var dateStr='';
								if(parseInt(weekday) == 1){
									var array = now.split(",");
									weekdayStr=array[0];
									dateStr=array[1];
								} else {
									dateStr=now;
								}
								
								var dateArray=dateStr.split("-");
								if(!isNaN(dateArray[0])){
									dateArray[0]=Number(dateArray[0]).toString();
								}
								if(!isNaN(dateArray[1])){
									dateArray[1]=Number(dateArray[1]).toString();
								}
								if(!isNaN(dateArray[2])){
									dateArray[2]=Number(dateArray[2]).toString();
								}
								
								if(weekdayStr == ''){
									now=dateArray[0]+'-'+dateArray[1]+'-'+dateArray[2];	
								} else {
									now=weekdayStr+', '+dateArray[0]+'-'+dateArray[1]+'-'+dateArray[2];		
								}
									
							}
							dnt=now.replace(/-/,"<?php echo $separator ?>");
							dnt=dnt.replace(/-/,"<?php echo $separator ?>");
							//console.log(dnt);
							
							document.getElementById(span_id).innerHTML=time;
							
							
							<?php if($date){?>
							document.getElementById("date_"+id[1]).innerHTML=dnt;
							<?php } ?>
					}
				}
				
				
			</script>
           		
		   <?php if ( $title ) {
			echo $args['before_title'] .'<span class="wid_title_color">'. $title .'</span>' . $args['after_title'];
			}?>
        
			<ul class="dt_clocks" >
            <!------------clocks-------->
            <?php for($i=1;$i<=4;$i++){?>	
         		<?php if(get_option( 'dt_clock'.$i.'_show' )){?>
				<li><div id="dt_clock_<?php echo $i; ?>">
                	<?php if($align<=2){ if($align==1){?>
                <div><?php if(get_option('dt_clock'.$i.'_text')!=''){ echo get_option('dt_clock'.$i.'_text'); } ?> <!--top--></div>
                <?php }}else {if($align==3){ ?>
                <?php if(get_option('dt_clock'.$i.'_text')!=''){ echo get_option('dt_clock'.$i.'_text'); } ?><!--left-->  
                <?php }} ?>
                <span id="clock_<?php echo $i; ?>" class="digits"></span>
                <?php if($align<=2){if($align==2){?>
                <div><?php if(get_option('dt_clock'.$i.'_text')!=''){ echo get_option('dt_clock'.$i.'_text'); } ?> <!--bottom--></div>
                <?php }}else {if($align==4){ ?>
                <?php if(get_option('dt_clock'.$i.'_text')!=''){ echo get_option('dt_clock'.$i.'_text'); } ?><!--right-->  
                <?php }} ?>
                <?php if($date){?>
                <div class="dt_date" id="date_<?php echo $i; ?>"> </div>
                <?php }?>
                <script>
				var t1 = setInterval(function(){DisplayTime('clock_<?php echo $i; ?>','<?php echo get_option('dt_clock'.$i.'_timezone'); ?>')},500);
				</script>
                </div></li>	
                <?php }//end of if for perticular clock ?>
            <?php }//end of for loop ?>
            <!------------clocks--------> 
			
            </ul>			
			
            <?php echo $args['after_widget']; ?>
		<?php }	
			
		
		function update($new_instance, $old_instance) {
		// save widget options
			$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
		}
		
		
		function form($instance) {
		// form to display widget settings in WordPress admin
			$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
		}
		
		
	}//end of class Dtw_Clock_Widget
	
	
	//For Adding Shortcode
	
		function DT_World_Clock_Shortcode($atts = [], $content = null, $tag = '') {
			wp_enqueue_script('moment_js');
        	wp_enqueue_script('moment-timezone-all-years_js');
			$atts[0]=implode(" ",$atts);
			//print_r($atts);
			//echo count($atts);
				if(!empty($atts)){
					$format=get_option('dt_format');
					$layout=get_option('dt_layout');			
					$align=get_option('dt_align');
					$sec=get_option( 'dt_sec' );
					$zeros=get_option( 'dt_zeros' );
					$date=get_option( 'dt_date' );
					$separator=get_option( 'dt_date_separator' )=="space" ? " " : get_option( 'dt_date_separator' );	
					if($align == '') $align =1;			
					/*$clock1_show=get_option( 'dt_clock1_show' );
					$clock2_show=get_option( 'dt_clock2_show' );
					$clock3_show=get_option( 'dt_clock3_show' );
					$clock4_show=get_option( 'dt_clock4_show' );*/
					$weekday=get_option( 'dt_week_day' );
					
					global $date_formats;
					$title = apply_filters('widget_title', $instance['title']);		
					echo $args['before_widget'];  
					
					$dt_clock_color=get_option( 'dt_clock_color' );  
					if(!$dt_clock_color)  {$dt_clock_color='#000';}   
					
					$dt_wid_title_color=get_option( 'dt_wid_title_color' );  
					if(!$dt_wid_title_color)  {$dt_wid_title_color='#000';}              
					?>					
								
					
					<style>
						.dt_clocks{
							list-style:none;
							margin:0;
							color:<?php echo $dt_clock_color; ?>;
							
						}
						.dt_clocks li
						{
							/* vertical */
							<?php if($layout==2){?>display:inline-block;<?php }?>	
							padding:2px;	
							margin-right:10px;					
						}
						.digits
						{
							font-size:18px;
						}
						.wid_title_color{
							color:<?php echo $dt_wid_title_color; ?>;
						}
						.display_left{
							display:inline-block;
							padding-right:5px;
						}
						.display_right{
							display:inline-block;
							padding-left:5px;
						}
					</style>
					<script>
					var moment='';
					function DisplayTime_shortcode(span_id,timezone)
						{
							if(moment !== null && typeof moment !== 'undefined' && moment !== undefined && moment != '' && moment.tz){
									id=span_id.split("_");
									var now=moment.tz(timezone).format("<?php if($weekday){ ?>dddd, <?php } ?><?php echo $date;?>");
									var ulzero='y';
									var weekday="<?php echo $weekday; ?>";
									//alert(now);
									var time=null;
									<?php if($format==1){?>
										time=moment.tz(timezone).format("HH:mm:ss");
										<?php if(!$sec){?>
											time=moment.tz(timezone).format("HH:mm");
										<?php } ?>
										
										<?php if($zeros==2){?>
											time=moment.tz(timezone).format("H:mm:ss");
											<?php if(!$sec){?>
											time=moment.tz(timezone).format("H:mm");
											<?php } ?>
										<?php } ?>
										<?php if($zeros==3){?>
											time=moment.tz(timezone).format("H:m:s");
											ulzero='n';
											<?php if(!$sec){?>
											time=moment.tz(timezone).format("H:m");
											<?php } ?>
										<?php } ?>
										
									<?php }else{?>
										time=moment.tz(timezone).format("hh:mm:ss A");
										<?php if(!$sec){?>
											time=moment.tz(timezone).format("hh:mm A");
											<?php } ?>
										<?php if($zeros==2){?>
											time=moment.tz(timezone).format("h:mm:ss A");
											<?php if(!$sec){?>
											time=moment.tz(timezone).format("h:mm A");
											<?php } ?>
										<?php } ?>
										<?php if($zeros==3){?>
											time=moment.tz(timezone).format("h:m:s A");
											ulzero='n';
											<?php if(!$sec){?>
											time=moment.tz(timezone).format("h:m A");
											<?php } ?>
										<?php } ?>
									<?php }?>
									
									//dnt=now.split(new RegExp('[-+T]','g'));
									
									if(ulzero == 'n'){
										var weekdayStr='';
										var dateStr='';
										if(parseInt(weekday) == 1){
											var array = now.split(",");
											weekdayStr=array[0];
											dateStr=array[1];
										} else {
											dateStr=now;
										}
										
										var dateArray=dateStr.split("-");
										if(!isNaN(dateArray[0])){
											dateArray[0]=Number(dateArray[0]).toString();
										}
										if(!isNaN(dateArray[1])){
											dateArray[1]=Number(dateArray[1]).toString();
										}
										if(!isNaN(dateArray[2])){
											dateArray[2]=Number(dateArray[2]).toString();
										}
										
										if(weekdayStr == ''){
											now=dateArray[0]+'-'+dateArray[1]+'-'+dateArray[2];	
										} else {
											now=weekdayStr+', '+dateArray[0]+'-'+dateArray[1]+'-'+dateArray[2];		
										}
											
									}
									dnt=now.replace(/-/,"<?php echo $separator ?>");
									dnt=dnt.replace(/-/,"<?php echo $separator ?>");
									//console.log(dnt);
								//	alert(span_id);
									//alert("date_"+id[1]);
									
									var classArray = document.getElementsByClassName(span_id);
									for(i=0; i < classArray.length; i++){
										document.getElementsByClassName(span_id)[i].innerHTML=time;
										<?php if($date){?>
										document.getElementsByClassName("date_"+id[1])[i].innerHTML=dnt;
										<?php } ?>
									}
							}
						}
					</script>
					<?php
					$clock='';
					$clock .='<ul class="dt_clocks" >';
					static $j=1000;
					$k=1;
					
					if($atts[0] != ''){
						$atts[0]=trim($atts[0],"'\"");
						$attribute=explode(',',$atts[0]);
						
						 for($i=0; $i < count($attribute); $i++){	
						 		$title='';
								$timeZone='';
								 if( $attribute[$i] != ''){
										   $keyValue=$attribute[$i];
									 	   $keyValueArray=explode(":",$keyValue);
										   if(count($keyValueArray) == 2){
											   $title=trim($keyValueArray[0]);
											   $timeZone=trim($keyValueArray[1]);
										   } else {
											   $title=trim($keyValueArray[0]);
											   $timeZone=trim($keyValueArray[0]);
										   }
									 
										  $clock .='<li><div class="dt_clock_'.$j.'">';
									
										  if($align<=2){ if($align==1){
													if($title != ''){  $clock .='<div>'.$title.'</div>'; };
										  }}else {if($align==3){
												  if($title != ''){ $clock .='<div class="display_left">'.$title.'</div>'; } 
										  }} 
									 
										 $clock .='<span class="digits clock_'.$j.'" ></span>';
										 
										if($align<=2){if($align==2){
													if($title != ''){ $clock .='<div>'.$title.'</div>'; };
										 }}else {if($align==4){ 
											   if($title != ''){ $clock .='<div class="display_right">'.$title.'</div>'; }  
										}}
										
										 if($date){
											$clock .='<div  class="dt_date date_'.$j.'"> </div>';
										 }
						?>
									<script>
									var t1 = setInterval(function(){DisplayTime_shortcode('clock_<?php echo $j; ?>','<?php echo $timeZone; ?>')},500);
									</script>
						<?php 
									$clock .='</div></li>';
									
								}//end of if for perticular clock 
								 $j++; $k++;
						   }//end of for loop 
					}
					$clock .='</ul>';			
					
		
			 
					return $clock;  
					
				}
		}
	
	
	add_shortcode('DT_World_Clock', 'DT_World_Clock_Shortcode');
	
	//End of Shortcode
	
	
	
	
	function dt_clock_register_widget()
	{


		register_widget( 'Dtw_Clock_Widget' );
	}
	/* Load the widget */
	add_action( 'widgets_init', 'dt_clock_register_widget' );		
	
?>