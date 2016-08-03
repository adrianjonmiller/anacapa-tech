<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 * Template Name: No Sidebar
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<div class="grid">
	<div class="col-3-4">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<h2>
		<?php the_title(); ?>
	</h2>
	<article class="module white-background">
		<p>
			All fields are required
		</p>
		<form data-behavior="freeSample">
			<div class="grid">
				<div class="col-1-2">
					<fieldset>
						<legend>First Name</legend>
						<input type="text" name="first_name" value="" required>
					</fieldset>
				</div>
				<div class="col-1-2">
					<fieldset>
						<legend>Last Name</legend>
						<input type="text" name="last_name" value="" required>
					</fieldset>
				</div>
			</div>

			<fieldset>
				<legend>Company</legend>
				<input type="text" name="company_name" value="" required>
			</fieldset>

			<div class="grid" style="margin-top: 2rem;">
				<div class="col-1-2">
					<fieldset>
						<legend>Email</legend>
						<input type="text" name="email" value="" required>
					</fieldset>
				</div>
				<div class="col-1-2">
					<fieldset>
						<legend>Phone</legend>
						<input type="text" name="phone_number" value="" required>
					</fieldset>
				</div>
			</div>

			<fieldset>
				<legend>Mailing Address</legend>
				<label>Address
					<input type="text" name="address" value="" required>
				</label>
				<div class="grid">
					<div class="col-1-2">
						<label>City
							<input type="text" name="city" value="" required>
						</label>
					</div>
					<div class="col-1-4">
						<label>
							State
							<select name="state" required>
								<option value="AL">Alabama</option>
								<option value="AK">Alaska</option>
								<option value="AZ">Arizona</option>
								<option value="AR">Arkansas</option>
								<option value="CA">California</option>
								<option value="CO">Colorado</option>
								<option value="CT">Connecticut</option>
								<option value="DE">Delaware</option>
								<option value="DC">District of Columbia</option>
								<option value="FL">Florida</option>
								<option value="GA">Georgia</option>
								<option value="HI">Hawaii</option>
								<option value="ID">Idaho</option>
								<option value="IL">Illinois</option>
								<option value="IN">Indiana</option>
								<option value="IA">Iowa</option>
								<option value="KS">Kansas</option>
								<option value="KY">Kentucky</option>
								<option value="LA">Louisiana</option>
								<option value="ME">Maine</option>
								<option value="MD">Maryland</option>
								<option value="MA">Massachusetts</option>
								<option value="MI">Michigan</option>
								<option value="MN">Minnesota</option>
								<option value="MS">Mississippi</option>
								<option value="MO">Missouri</option>
								<option value="MT">Montana</option>
								<option value="NE">Nebraska</option>
								<option value="NV">Nevada</option>
								<option value="NH">New Hampshire</option>
								<option value="NJ">New Jersey</option>
								<option value="NM">New Mexico</option>
								<option value="NY">New York</option>
								<option value="NC">North Carolina</option>
								<option value="ND">North Dakota</option>
								<option value="OH">Ohio</option>
								<option value="OK">Oklahoma</option>
								<option value="OR">Oregon</option>
								<option value="PA">Pennsylvania</option>
								<option value="RI">Rhode Island</option>
								<option value="SC">South Carolina</option>
								<option value="SD">South Dakota</option>
								<option value="TN">Tennessee</option>
								<option value="TX">Texas</option>
								<option value="UT">Utah</option>
								<option value="VT">Vermont</option>
								<option value="VA">Virginia</option>
								<option value="WA">Washington</option>
								<option value="WV">West Virginia</option>
								<option value="WI">Wisconsin</option>
								<option value="WY">Wyoming</option>
							</select>
						</label>
					</div>
					<div class="col-1-4">
						<label>
							Zip
							<input type="text" name="zip_postal_code" value="" required>
						</label>
					</div>
				</div>

				<label>
					Country
					<input type="text" name="country" value="United States of America" required>
				</label>
			</fieldset>

			<fieldset>
				<legend>Product Type</legend>
				<select name="product_type" required>
		      <option></option>
		      <option value="1">4004C (Dispensing Cap) 4oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="2">4004SC (Finger Sprayer) 4oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="3">4008C (Dispensing Cap) 8oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="4">4008SC (Finger Sprayer) 8oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="5">4008TC(Trigger Sprayer) 8oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="6">4012SC (Trigger Sprayer) 8oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="7">4016C (Dispending Cap) 15oz Anasept® Antimicrobial Skin and Wound Cleanser</option>
		      <option value="8">41601C (Spikable Cap) 16oz Anasept® Antimicrobial Wound Irrigation Soultion</option>
		      <option value="9">5015G (Tube) 1.5oz Anasept® Antimicrobial Skin and Wound Gel</option>
		      <option value="10">5003G (Tube) 3oz Anasept® Antimicrobial Skin and Wound Gel</option>
		      <option value="11">3015S (Tube) 1.5oz Silver-Sept® Antimicrobial Skin and Wound Gel</option>
		      <option value="12">3003S (Tube) 3oz Silver-Sept® Antimicrobial Skin and Wound Gel</option>
		      <option value="13">1002A (Sprayer) 2oz Sani-Zone™ Odor Eliminator Air Spray</option>
		      <option value="14">1008A (Sprayer) 8oz Sani-Zone™ Odor Eliminator Air Spray</option>
		      <option value="15">1002-OD (Dispensing Cap) 2oz Sani-Zone™ Ostomy Appliance Deodorant</option>
		      <option value="16">1008-OD (Dispensing Cap) 8oz Sani-Zone™ Ostomy Appliance</option>
		      <option value="17">6000TX (Small) Staytex Elastic Tubular Dressing</option>
		      <option value="18">6006tXP (Medium) Staytex Elastic Tubular Dressing</option>
		      <option value="19">6010FTC (120’ roll in Dispenser Box) Staytex Elastic Tubular Dressing</option>
		      <option value="20">7004VSP (Finger Sprayer) 4oz Biovetrex™ Antimicrobial Skin and Wound Cleanser</option>
		      <option value="21">7088VTP (Trigger Sprayer) 8oz Biovetrex™ Antimicrobial Skin and Wound Cleanser</option>
		      <option value="22">9015VGP (Tube) 1.5oz Biovetrex™ Antimicrobial Skin and Wound Gel</option>
		      <option value="23">9033VGP (Tube) 3oz Biovetrex™ Antimicrobial Skin and Wound Gel</option>
		    </select>
			</fieldset>
			<fieldset>
				<input type="submit" name="name" value="Submit">
			</fieldset>
		</form>
	</article>
<?php endwhile; ?>
	</div>
	<div class="col-1-4">
		<?php include ('parts/sidebar_primary.php'); ?>
	</div>
</div>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>
