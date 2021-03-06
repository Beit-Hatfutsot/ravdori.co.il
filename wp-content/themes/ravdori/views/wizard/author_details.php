<?php
/**
 *
 * @author     Htmline (Roy Hizkya)
 * @copyright  Copyright (c) 2015 Beit Hatfutsot Israel. (http://www.bh.org.il)
 * @version    1.0
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();
?>

<?php $locale = get_language_locale_filename_by_get_param(); ?>
<?php $user_id  = get_current_user_id(); ?>

    <script>


        var $ = jQuery

        /* Fields validation */
        $().ready(function () {


                // validate the form's fields
                $("#wizard-form-step2").validate({

                    errorPlacement: function (error, element) {
                        // Append error within linked label
                        $(element)
                            .closest("form")
                            .find("label[for='" + element.attr("id") + "']")
                            .append(error)
                    },

                    errorElement: "span",
                    rules: {

                                <?php echo IWizardStep2Fields::FIRST_NAME;?>:
                                {
                                    required: true,
                                    minlength: 2,
                                    onlyLetters: true
                                },

                                <?php echo IWizardStep2Fields::LAST_NAME;?>:
                                {
                                    required: true,
                                    minlength: 2,
                                    onlyLetters: true
                                },

                                <?php echo IWizardStep2Fields::ADDITIONAL_NAME;?>:
                                {
                                    minlength: 2,
                                    onlyLetters: true,
                                },

                                <?php echo IWizardStep2Fields::BIRTH_CITY;?>:
                                {
                                   onlyLetters: true,
                                },

                           },

            messages: {

            <?php echo IWizardStep2Fields::FIRST_NAME;?>:
            {
                required: "<?php BH__e('שדה חובה' , 'BH', $locale);?>",
                minlength: "<?php BH__e('שם פרטי חייב להכיל לפחות 2 אותיות' , 'BH', $locale);?>",
            },

            <?php echo IWizardStep2Fields::LAST_NAME;?>:
            {
                required: "<?php BH__e('שדה חובה' , 'BH', $locale);?>",
                minlength: "<?php BH__e('שם משפחה חייב להכיל לפחות 2 אותיות' , 'BH', $locale);?>",
            },

            <?php echo IWizardStep2Fields::ADDITIONAL_NAME;?>:
            {
                minlength: "<?php BH__e('שם חייב להכיל לפחות 2 אותיות' , 'BH', $locale);?>",
            },

        }
        });


        $('#draft_story').click(function() {
            $('#wizard-form-step2').append('<input type="hidden" name=" <?php echo IWizardStep2Fields::STORY_LOADED;?>" value="' + $('#draft_story').data("id") + '">');
            $('#wizard-form-step2').submit();
        });



        $("#wizard-form-step2").on('submit', function(e){

            var isFormValid = true;

            // If the user choose immegration year
            if ($("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE ?> option:selected").val() != -1 )
            {

                var immegrationYear =  $("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE ?>").val();
                var birthYear       =  $("#<?php echo IWizardStep2Fields::BIRTHDAY ?>").val();


                if ( immegrationYear < birthYear  )
                {
                    $("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE ?>").before("<label class='quote_error'><span class='error'><?php BH__e('שנת לידה חייבת להיות גדולה משנת עלייה' , 'BH', $locale);?></span></label>");
                    isFormValid = false;
                }

            }

            // if the user did not choose a birthday
            if ($("#<?php echo IWizardStep2Fields::BIRTHDAY ?> option:selected").val() == -1 )
            {
                $("#<?php echo IWizardStep2Fields::BIRTHDAY ?>").before("<label class='quote_error'><span class='error'><?php BH__e('שדה חובה' , 'BH', $locale);?></span></label>");
                isFormValid = false;
            }

            if ( document.getElementById('do-saving') )
            {
                isFormValid = true;
                return true;
            }

            return isFormValid;
        });


        // Prevent the user from entering wrong data
        var selectors  = "#<?php echo IWizardStep2Fields::FIRST_NAME ?>,";
            selectors += "#<?php echo IWizardStep2Fields::LAST_NAME ?>,";
            selectors += "#<?php echo IWizardStep2Fields::ADDITIONAL_NAME ?>,";
            selectors += "#<?php echo IWizardStep2Fields::BIRTH_CITY ?>";


        $(selectors).alphanum({
            allow              : '()[]/\\,\'- "',
            disallow           : '!@#$%^&*+=[]\\;/{}|":<>?~`._',
            allowSpace         : false,
            allowNumeric       : false,
            allowUpper         : true,
            allowLower         : true,
            allowCaseless      : true,
            allowLatin         : true,
            allowOtherCharSets : true,
            forceUpper         : false,
            forceLower         : false,
            maxLength          : NaN
        });


        // Make the select boxes searchable

        $("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>").chosen( { placeholder_text_single : "<?php BH__e("בחרו ארץ לידה" , "BH", $locale);?>", no_results_text: "<?php BH__e("לא נמצאו תוצאות ל - " , "BH", $locale);?>"  } );
        $("#<?php echo IWizardStep2Fields::BIRTHDAY ?>").chosen( { placeholder_text_single : "<?php BH__e("בחרו שנת לידה" , "BH", $locale);?>", no_results_text: "<?php BH__e("לא נמצאו תוצאות ל - " , "BH", $locale);?>"  } );
        $("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE ?>").chosen( { placeholder_text_single : "<?php BH__e("בחרו שנת עליה" , "BH", $locale);?>", no_results_text: "<?php BH__e("לא נמצאו תוצאות ל - " , "BH", $locale);?>"  } );
        $("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>").chosen( { placeholder_text_single : "", no_results_text: "<?php BH__e("לא נמצאו תוצאות ל - " , "BH", $locale);?>"  } );



        $('#submitSave').on('click', function(e){

            $('<input />').attr('type', 'hidden')
                .attr('id', 'do-saving')
                .attr('name', 'do-saving')
                .attr('value', 'save')
                .appendTo('#wizard-form-step2');
        });


        updateChosen();

        // The countries select
        jQuery("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>").change(function () { updateChosen(); }); // Change event

        toggleImmigrationSelect();

        $("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY?>").change(function () {
            toggleImmigrationSelect();
        });
		
		 
		 // If the user is a returning user, and he already accepted the notice
		 if ( sessionStorage.getItem('user-notice-accepted') !== '<?php echo $user_id;?>' &&  $('.wizard-full-screen-notice').length > 0 ) {
			 $('.wizard-full-screen-notice').removeClass('hidden');
		 }
		 
		 
        $('#close-notice').on('click', function(e){

			$('.wizard-full-screen-notice').addClass('hidden');
			sessionStorage.setItem('user-notice-accepted','<?php echo $user_id;?>');
            
        });
		

        }); //Ready


        function updateChosen()
        {
            sortSelectByTermName();
            $("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>").trigger("chosen:updated");
            $("#<?php echo IWizardStep2Fields::BIRTHDAY?>").trigger("chosen:updated");
			$("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE?>").trigger("chosen:updated");
        }


        function sortSelectByTermName()
        {

            // Loop for each select element on the page.
            $('select').each(function() {
                // Keep track of the selected option.
                var selectedValue = $(this).val();
                // Sort all the options by text.
                $(this).html($('option', $(this)).sort(function(a, b) {
                    return $(a).text() == $(b).text() ? 0 : $(a).text() < $(b).text() ? -1 : 1
                }));
                // Select one option.
                $(this).val(selectedValue);
            });
        }



        // Show\hide the immigration select
        function toggleImmigrationSelect()
        {
            if ($("#<?php echo IWizardStep2Fields::BIRTH_COUNTRY?>").val() != 3640) // If not Israel
                $("#immigration-select-container").show();
            else {
                $("#<?php echo IWizardStep2Fields::IMMIGRATION_DATE?>").val(-1).trigger("chosen:updated");
                $("#immigration-select-container").hide();
            }
        }



    </script>

<?php

	$pending_stories_count = 0;
	$query = null;
	
	
	// Get all the pending stories
	if (  isset( $data['user']['pending']  )   ):

		$query = $data['user']['pending'];
		$pending_stories_count = $query->post_count;
		
	endif;

	
?>

<?php $errors = isset($data['errors']) ? $data['errors'] : null; ?>
    <section class="page-content">

        <div class="container">

            <div class="row">
				

				<?php 
				
				$user_drafts_and_published_stories_count = 0;
				
				$published_stories_query_obj  = $data['user']['publish'];
				
				if ( isset( $published_stories_query_obj ) ) {
					$user_drafts_and_published_stories_count += $published_stories_query_obj->found_posts;
				}
				
				$draft_stories_query_obj  = $data['user']['draft'];
				
				if ( isset( $draft_stories_query_obj ) ) {
					$user_drafts_and_published_stories_count += $draft_stories_query_obj->found_posts;
				}
				
				
				$user_id   = $query->query_vars['author'];
				$user_info = get_userdata($user_id);
		
				if ( $user_info != false AND $user_drafts_and_published_stories_count > 0 ):?>
					
					<?php 
						$adult_email = $user_info->user_email;
						$user_name   = $user_info->first_name . ' ' . $user_info->last_name;
					?>
				
					<div class="wizard-full-screen-notice hidden">
							<div class="wizard-full-screen-notice__title">
								<?php BH__e('שימו לב' , 'BH', $locale);?>
							</div>
							
							<div class="wizard-full-screen-notice__text">
								<p>
									<?php printf('%s <span class="notify-strong">%s</span>', BH__("כתובת הדואר האלקטרוני שהוקלדה היא:" , "BH", $locale)
														, $adult_email); ?>
								</p>

								<p>								
									<?php printf('%s<span class="notify-strong">%s</span>.', BH__("הכתובת משויכת ל" , "BH", $locale)
									, $user_name); ?>
								</p>
								
								<p>
									<?php BH__e('במידה ומדובר באדם אחר, לחץ על כפתור התנתקות והזן כתובת דוא"ל חלופית.' , "BH", $locale); ?>					
								</p>	
								
								<p>
									<?php BH__e('בכל מקרה אחר, לחץ על סגור' , "BH", $locale); ?>					
								</p>	
								
								<div class="notice-buttons-container">
								
								<?php 
									$wizard_url = get_field('acf-options-wizard-page-url','options'); 
									$wizard_url = $wizard_url? $wizard_url : home_url();
								?>
								
									<a class="btn logout-link" href="<?php echo wp_logout_url( $wizard_url ); ?>"><?php BH__e('התנתקות' , 'BH' ,  $locale ); ?></a>
									<div class="btn" id="close-notice"><?php BH__e('סגור' , 'BH' ,  $locale ); ?></div>
								</div>
								
							</div>
							
							
					</div>
				<?php endif; ?>
				
                <?Php
                //Show the prograss bar
                include ( WIZARD_VIEWS . '/components/progressbar.php' );

                global $wizardSessionManager;

                ?>
                <div class="col-xs-12">

				
				<?php 
					// If we don't have pending stories,
					// show the form as usual;
					if ( $pending_stories_count == 0 ): 
				
				?>
					<form id="wizard-form-step2" class="wizard-form" method="post" action="">
				<?php 
					// Show just a div instead
					else: ?>	
						<div id="wizard-form-step2" class="wizard-form">
				<?php endif; ?>		
				
				
				
                <div class="title">
                    <h2><?php echo '2 - ' . $wizard_steps_captions[IWizardStep2Fields::ID - 1]; ?></h2>
                </div>

                <?php
                $nextButtonCaption = BH__("הבא" , "BH" , $locale);

                if (isset( $data['user']['exist'] ) AND $data['user']['exist'] == true AND $wizardSessionManager->isStepAvailable( IWizardStep3Fields::ID ) == false)
                {
                    $nextButtonCaption = BH__("יצירת סיפור חדש" , "BH", $locale);
                }
                ?>

                    <?php $step2Data = $wizardSessionManager->getStepData( IWizardStep2Fields::ID );  ?>
                    <?php  $firstName = isset ( $step2Data[IWizardStep2Fields::FIRST_NAME] ) ?  $step2Data[IWizardStep2Fields::FIRST_NAME] : '';  ?>

                    <!-- First name -->
                    <div class="element-input" >
                        <label for="<?php echo IWizardStep2Fields::FIRST_NAME;?>" class="title"><?php BH__e('* שם פרטי:' , 'BH', $locale);?></label>
                        <input id="<?php echo IWizardStep2Fields::FIRST_NAME;?>"
                               class="large" type="text"
                               name="<?php echo IWizardStep2Fields::FIRST_NAME;?>"
                               value="<?php echo stripslashes ($firstName); ?>"
                               placeholder="<?php BH__e('ושם שני אם יודעים' , 'BH', $locale);?>"/>
                    </div>

                    <?php  $lastName = isset ( $step2Data[IWizardStep2Fields::LAST_NAME] ) ?  $step2Data[IWizardStep2Fields::LAST_NAME] : '';  ?>

                    <!-- Last name -->
                    <div class="element-input" >
                        <label for="<?php echo IWizardStep2Fields::LAST_NAME;?>" class="title"><?php BH__e('* שם משפחה:' , 'BH', $locale);?></label>
                        <input id="<?php echo IWizardStep2Fields::LAST_NAME;?>"
                               class="large"
                               type="text"
                               name="<?php echo IWizardStep2Fields::LAST_NAME;?>"
                               value="<?php echo stripslashes ($lastName); ?>"
                               placeholder="<?php BH__e('שם המשפחה שלי' , 'BH', $locale);?>"/>
                    </div>


                    <?php  $additionalName = isset ( $step2Data[IWizardStep2Fields::ADDITIONAL_NAME]  ) ?  $step2Data[IWizardStep2Fields::ADDITIONAL_NAME] : '';  ?>

                    <!-- Name before marrige -->
                    <div class="element-input" >
                        <label for="<?php echo IWizardStep2Fields::ADDITIONAL_NAME;?>" class="title"><?php BH__e('שם משפחה לפני הנישואין או שינוי:' , 'BH', $locale);?></label>
                        <input id="<?php echo IWizardStep2Fields::ADDITIONAL_NAME;?>"
                               class="large"
                               type="text"
                               name="<?php echo IWizardStep2Fields::ADDITIONAL_NAME;?>" value="<?php echo stripslashes ($additionalName); ?>"
                               placeholder="<?php BH__e('שם נעורים' , 'BH', $locale);?>"/>
                    </div>

                    <!-- Birthday -->
                    <div class="element-select" title="<?php BH__e('שנת לידה' , 'BH', $locale);?>">


                        <div class="large">
                            <span class="wizard-select-theme">
                                <label for="<?php echo IWizardStep2Fields::BIRTHDAY; ?>" class="title"><?php BH__e('* שנת לידה' , 'BH', $locale);?></label>
                            <select id="<?php echo IWizardStep2Fields::BIRTHDAY; ?>"  name="<?php echo IWizardStep2Fields::BIRTHDAY; ?>" class="chosen-rtl">
                                    <?php
                                    // use this to set an option as selected (ie you are pulling existing values out of the database)
                                    $already_selected_value = isset( $step2Data[IWizardStep2Fields::BIRTHDAY] ) ? $step2Data[IWizardStep2Fields::BIRTHDAY] :  -1 ;
                                    $earliest_year = 1900;

                                    echo '<option  value="-1" ' . ( ( $already_selected_value == -1)  ? ' selected="true" ' : '' ) . '>' . BH__('בחרו שנת לידה', 'BH', $locale) . '</option>';

                                    foreach (range(date('Y'), $earliest_year) as $year)
                                    {
                                        echo '<option value="'. $year .'" '  .( ( intval( $year ) == intval( $already_selected_value ) ) ? ' selected="true" ' : '')  .  '>' . $year . '</option>';
                                    }

                                    ?>
                            </select>

                            </span>
                        </div>
                    </div>

                    <!-- Country -->
                    <div id="country-field" class="element-select" title="<?php BH__e('ארץ לידה' , 'BH', $locale);?>">
                        <label for="<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>" class="title"><?php BH__e('* ארץ לידה' , 'BH', $locale);?></label>

                        <div class="large">
			                <span class="wizard-select-theme">
			                    <select id="<?php echo IWizardStep2Fields::BIRTH_COUNTRY?>" name="<?php echo IWizardStep2Fields::BIRTH_COUNTRY ?>" class="chosen-rtl">
                                    <?php $countries = $data['countries'];  ?>
                                    <?php $countryToShow = get_field( 'acf-options-wizard-step1-country' , 'options' ); ?>
                                    <?php $birthCountry  = isset ( $step2Data[IWizardStep2Fields::BIRTH_COUNTRY ]  ) ?  $step2Data[ IWizardStep2Fields::BIRTH_COUNTRY ] : $countryToShow;   ?>
                                    <?php foreach ($countries as $country): ?>

                                        <option <?php echo ($country->term_id == $birthCountry ? 'selected = true' : '' );?>
                                            value="<?php echo $country->term_id; ?>">
                                            <?php echo $country->name; ?>

                                        </option>
                                        <br/>

                                    <?php endforeach ?>
                                </select>
			                </span>
                        </div>
                    </div>




                    <!-- City -->

                    <?php  $birthCity = isset ( $step2Data[IWizardStep2Fields::BIRTH_CITY] ) ?  $step2Data[IWizardStep2Fields::BIRTH_CITY] : '';  ?>

                    <div class="element-input" >
                        <label for="<?php echo IWizardStep2Fields::BIRTH_CITY;?>" class="title"><?php BH__e('עיר לידה' , 'BH', $locale);?></label>
                        <input id="<?php echo IWizardStep2Fields::BIRTH_CITY;?>" class="large" type="text"  value="<?php echo stripslashes ($birthCity); ?>"  name="<?php echo IWizardStep2Fields::BIRTH_CITY;?>" />
                    </div>

                    <!-- Immigration  -->
                    <div id="immigration-select-container" class="element-select" title="<?php BH__e('שנת עליה' , 'BH', $locale);?>">
                        <label for="<?php echo IWizardStep2Fields::IMMIGRATION_DATE; ?>" class="title"><?php BH__e('שנת עליה' , 'BH', $locale);?></label>

                        <div class="large">
                            <span class="wizard-select-theme">
                            <select id="<?php echo IWizardStep2Fields::IMMIGRATION_DATE; ?>"  name="<?php echo IWizardStep2Fields::IMMIGRATION_DATE; ?>" class="chosen-rtl">
                                <?php
                                // use this to set an option as selected (ie you are pulling existing values out of the database)
                                $already_selected_value = isset( $step2Data[ IWizardStep2Fields::IMMIGRATION_DATE] ) ? $step2Data[ IWizardStep2Fields::IMMIGRATION_DATE] :  -1 ;
                                $earliest_year = 1900;

                                echo '<option value="-1" ' . ( ( $already_selected_value == -1)  ? ' selected="true" ' : '' ) . '>' . BH__('בחרו שנת עלייה' , 'BH', $locale)  . '</option>';

                                foreach (range(date('Y'), $earliest_year) as $year)
                                {
                                    echo '<option value="'. $year .'" '  .( ( intval( $year ) == intval( $already_selected_value ) ) ? ' selected="true" ' : '')  .  '>' . $year . '</option>';
                                }

                                ?>
                            </select>

                            </span>
                        </div>
                    </div>


                    <?php $nextButtonCaption = BH__("שמור והמשך &#9664;" , 'BH', $locale);?>
                    <!-- User stories -->
                    <?php  if (  isset( $data['user']['exist'] ) AND $data['user']['exist'] == true  ):  ?>

                        <?php
                              /* if ( $wizardSessionManager->isStepAvailable( IWizardStep3Fields::ID ) == false)
                               {
                                    $nextButtonCaption = "יצירת סיפור חדש &#9664;";
                               }*/

                            ?>


                        <div style="text-align: center;">

                            <?php

                                    // Get all the published stories, and show them
                                    if (  isset( $data['user']['publish']  )   ):

                                        $query = $data['user']['publish'];

                                        if( $query->have_posts() )
                                        {
                                            echo '<h2 class="pulse red-title" style="text-align:center;">' . BH__('סיפורים מפורסמים','BH', $locale) . '</h2>';
                                            echo '<ul>';
                                            while($query->have_posts()) : $query->the_post();
                                                echo '<li class="author-page-stories-list"><a href="' . esc_url( get_permalink() ) . '" target="_blank" style="cursor: pointer;" >' . get_the_title() . '</a></li>';
                                                // Load into session
                                            endwhile;
                                            echo '</ul>';
                                        }
                                    endif;

					   
									$existingStoryInput = '';
									
                                    // Get all the drafts
                                    if (  isset( $data['user']['draft']  )   ):

                                           $query = $data['user']['draft'];

                                           global $wizardSessionManager;
                                           $step4Data = $wizardSessionManager->getStepData( IWizardStep4Fields::ID );
                                           $currentPostId =  isset ( $step4Data[IWizardStep4Fields::POST_ID] ) ?  $step4Data[IWizardStep4Fields::POST_ID] : null;

                                        if( $query->have_posts() )
                                           {
                                               if ( empty($currentPostId) OR ( $currentPostId != get_the_ID() ) )
                                               {
                                                   //echo '<h2 class="pulse red-title" style="text-align:center;">' . BH__('סיפורים בתהליך כתיבה - כניסה חוזרת לסיפור שלי' , 'BH', $locale) . '</h2>';

												   $alert_style_tag_rtl = (get_language_locale_filename_by_get_param(true));
												   $alert_style_tag_rtl = ($alert_style_tag_rtl["dir"] == 'ltr') ? 'style="direction: ltr; font-size: 21px !important; line-height: 1.5em !important;."' : '';
												   
                                                   echo '<div class="alert alert-info existing-story-alert"' . ' ' . $alert_style_tag_rtl.  '>';
                                                   echo '<strong>' . BH__('שימו לב! ' , 'BH', $locale)  . '</strong>';
                                                   echo '<br/>';
                                                   echo '<p>';
                                                   echo BH__('להמשך טיפול בסיפורך הנמצא במצב טיוטא נא לחץ על שמור והמשך' , 'BH', $locale) . '<br>' . BH__('לא ניתן להתחיל בכתיבת סיפור חדש לפני שנשלח סיפור זה לפרסום','BH', $locale) . '<br>';
                                                   echo '</p>';
                                                   echo '</div>';
                                               }

                                               echo '<ul>';
                                                   while($query->have_posts()) : $query->the_post();

                                                       if ( empty($currentPostId) OR ( $currentPostId != get_the_ID() ) ) {

                                                           //echo '<li class="author-page-stories-list"><a id="draft_story" data-id="' . get_the_ID() . '" style="cursor: pointer;">' . get_the_title() . '</a></li>';
														   $existingStoryInput = '<input type="hidden" name="' . IWizardStep2Fields::STORY_LOADED . '" value="'  . get_the_ID() . '">';
														   break; // only one story allowed

                                                       }
                                                   endwhile;
                                               echo '</ul>';
                                           }
                                  endif;
                            ?>

                   
                        </div>

                    <?php endif; ?>




					<?php 
					
						// If we don't have pending stories,
						// show the form as usual
						if ( $pending_stories_count == 0 ): 
					?>


                    <div class="submit">
                        <input type="submit" class="next" value="<?php echo $nextButtonCaption; ?>"  style="float: left;margin-left: 23px;"/>
						<?php echo (isset($existingStoryInput) ? $existingStoryInput : ''); ?>
                        <input id="submitSave" type="submit" class="cancel" style="float: left;margin-left: 23px;background-color: #999999;" value="<?php BH__e('שמור' , 'BH', $locale);?>"/>
                    </div>

                    <input type="hidden" name="step" value="<?php echo IWizardStep3Fields::ID; ?>"/>
                    <?php wp_nonce_field( 'nonce_author_details_form_action' , 'nonce_author_details' ); ?>


                </form>
				
				<?php 
					// Else, close the openinig div, and hide the "next" button
					else:
				?>
				
				</div>
				
				<div class="author-page-stories-list alert alert-info existing-story-alert text-center voffset5">
					<?php BH__e('לא ניתן להעלות סיפור חדש כל עוד סיפורך הקודם טרם פורסם!', 'BH', $locale);  ?>
				</div>
				
				<?php endif; ?>

                <form name="step1"  class="wizard-form" method="post">
                    <input name="progstep" value="1" type="hidden">

                    <div class="submit" <?php echo ($pending_stories_count > 0) ? 'style="width:100%;"' : ''; ?> >
                        <input type="submit" class="back" value="<?php BH__e('&#9654; הקודם' , 'BH', $locale);?>"/>
                    </div>

                </form>

                </div>

            </div>
        </div>

    </section>
<?php  get_footer(); ?>