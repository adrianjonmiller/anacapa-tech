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
					<option value="Select Product from this list:" selected="selected">Select Product from this list:</option>
					<option value="Anasept: 4004SC 4 oz. (Sprayer)">Anasept: 4004SC 4 oz. (Sprayer)</option>
					<option value="Anasept Gel: 5015G 1.5 oz. (Tube)">Anasept Gel: 5015G 1.5 oz. (Tube)</option>
					<option value="Anasept: Irrigation 4160IC 16 oz.  (Spikeable Bottle)">Anasept: Irrigation 4160IC 16 oz.  (Spikeable Bottle)</option>
					<option value="Sani-Zone: 1002A 2 oz. (Spray)">Sani-Zone: 1002A 2 oz. (Spray)</option>
					<option value="Sani-Zone OAD:  1002-OD 2 oz. (Dropper Bottle)">Sani-Zone OAD:  1002-OD 2 oz. (Dropper Bottle)</option>
					<option value="Silver-Sept: 3015-S 1.5 oz (Tube)">Silver-Sept: 3015-S 1.5 oz (Tube)</option><option value="Staytex Precut 22&quot;  6000TX">Staytex Precut 22"  6000TX</option>
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
