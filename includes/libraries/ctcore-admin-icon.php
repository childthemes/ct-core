<?php

/**
 * Provide a admin area view for the Lightbox icon Picker
 *
 * This file is used to markup the admin-facing colorpicker
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Core
 * @subpackage CT_Core/inludes
 */
?>
<div id="colorpickerwrap" style="display:none">
	<div class="colorpickerwrap close">
		<header>
			<div class="cat-wrap">
				<select id="iconpicker-category" name="iconpicker-cat" class="widefat">
					<option value=""><?php esc_attr_e( 'All Categories', 'ctcore' ); ?></option>
					<option value="web-content"><?php esc_attr_e( 'Web Content', 'ctcore' ); ?></option>
					<option value="user-actions"><?php esc_attr_e( 'User Actions', 'ctcore' ); ?></option>
					<option value="message"><?php esc_attr_e( 'Messages', 'ctcore' ); ?></option>
					<option value="user-types"><?php esc_attr_e( 'User Types', 'ctcore' ); ?></option>
					<option value="gender"><?php esc_attr_e( 'Gender & Sexuality', 'ctcore' ); ?></option>
					<option value="layout"><?php esc_attr_e( 'Layout Adjustment', 'ctcore' ); ?></option>
					<option value="objects"><?php esc_attr_e( 'Objects', 'ctcore' ); ?></option>
					<option value="media"><?php esc_attr_e( 'Media', 'ctcore' ); ?></option>
					<option value="technologies"><?php esc_attr_e( 'Technologies', 'ctcore' ); ?></option>
					<option value="rating"><?php esc_attr_e( 'Rating', 'ctcore' ); ?></option>
					<option value="map"><?php esc_attr_e( 'Map', 'ctcore' ); ?></option>
					<option value="currency"><?php esc_attr_e( 'Currency', 'ctcore' ); ?></option>
					<option value="payment"><?php esc_attr_e( 'Payment Options', 'ctcore' ); ?></option>
					<option value="brands"><?php esc_attr_e( 'Brands', 'ctcore' ); ?></option>
				</select>
			</div>
			<div class="search-wrap" style="display:none">
				<input class="widefat" type="text" name="iconpicker-search" value="" placeholder="<?php esc_html_e('Search Icon...','ctcore'); ?>" readonly>
			</div>
		</header>
		<section id="iconlist">
			<div class="icon-cat-wrap web-content" data-cat="web">
        <div class="column"><i class="alarm icon"></i>Alarm</div>
        <div class="column"><i class="alarm slash icon"></i>Alarm Slash</div>
        <div class="column"><i class="alarm outline icon"></i>Alarm Outline</div>
        <div class="column"><i class="alarm slash outline icon"></i>Alarm Slash Outline</div>
        <div class="column"><i class="at icon"></i>At</div>
        <div class="column"><i class="browser icon"></i>Browser</div>
        <div class="column"><i class="bug icon"></i>Bug</div>
        <div class="column"><i class="calendar outline icon"></i>Calendar Outline</div>
        <div class="column"><i class="calendar icon"></i>Calendar</div>
        <div class="column"><i class="cloud icon"></i>Cloud</div>
        <div class="column"><i class="code icon"></i>Code</div>
        <div class="column"><i class="comment icon"></i>Comment</div>
        <div class="column"><i class="comments icon"></i>Comments</div>
        <div class="column"><i class="comment outline icon"></i>Comment Outline</div>
        <div class="column"><i class="comments outline icon"></i>Comments Outline</div>
        <div class="column"><i class="copyright icon"></i>Copyright</div>
        <div class="column"><i class="dashboard icon"></i>Dashboard</div>
        <div class="column"><i class="dropdown icon"></i>Dropdown</div>
        <div class="column"><i class="external square icon"></i>External Square</div>
        <div class="column"><i class="external icon"></i>External</div>
        <div class="column"><i class="eyedropper icon"></i>Eyedropper</div>
        <div class="column"><i class="feed icon"></i>Feed</div>
        <div class="column"><i class="find icon"></i>Find</div>
        <div class="column"><i class="heartbeat icon"></i>Heartbeat</div>
        <div class="column"><i class="history icon"></i>History</div>
        <div class="column"><i class="home icon"></i>Home</div>
        <div class="column"><i class="idea icon"></i>Idea</div>
        <div class="column"><i class="inbox icon"></i>Inbox</div>
        <div class="column"><i class="lab icon"></i>Lab</div>
        <div class="column"><i class="mail icon"></i>Mail</div>
        <div class="column"><i class="mail outline icon"></i>Mail Outline</div>
        <div class="column"><i class="mail square icon"></i>Mail Square</div>
        <div class="column"><i class="map icon"></i>Map</div>
        <div class="column"><i class="options icon"></i>Options</div>
        <div class="column"><i class="paint brush icon"></i>Paint Brush</div>
        <div class="column"><i class="payment icon"></i>Payment</div>
        <div class="column"><i class="phone icon"></i>Phone</div>
        <div class="column"><i class="phone square icon"></i>Phone Square</div>
        <div class="column"><i class="privacy icon"></i>Privacy</div>
        <div class="column"><i class="protect icon"></i>Protect</div>
        <div class="column"><i class="search icon"></i>Search</div>
        <div class="column"><i class="setting icon"></i>Setting</div>
        <div class="column"><i class="settings icon"></i>Settings</div>
        <div class="column"><i class="shop icon"></i>Shop</div>
        <div class="column"><i class="sidebar icon"></i>Sidebar</div>
        <div class="column"><i class="signal icon"></i>Signal</div>
        <div class="column"><i class="sitemap icon"></i>Sitemap</div>
        <div class="column"><i class="tag icon"></i>Tag</div>
        <div class="column"><i class="tags icon"></i>Tags</div>
        <div class="column"><i class="tasks icon"></i>Tasks</div>
        <div class="column"><i class="terminal icon"></i>Terminal</div>
        <div class="column"><i class="text telephone icon"></i>Text Telephone</div>
        <div class="column"><i class="ticket icon"></i>Ticket</div>
        <div class="column"><i class="trophy icon"></i>Trophy</div>
        <div class="column"><i class="wifi icon"></i>Wifi</div>
      </div>
			<div class="icon-cat-wrap user-actions" data-cat="user">
				<div class="column"><i class="adjust icon"></i>Adjust</div>
				<div class="column"><i class="add user icon"></i>Add User</div>
				<div class="column"><i class="add to cart icon"></i>Add to cart</div>
				<div class="column"><i class="archive icon"></i>Archive</div>
				<div class="column"><i class="ban icon"></i>Ban</div>
				<div class="column"><i class="bookmark icon"></i>Bookmark</div>
				<div class="column"><i class="call icon"></i>Call</div>
				<div class="column"><i class="call square icon"></i>Call Square</div>
				<div class="column"><i class="cloud download icon"></i>Cloud Download</div>
				<div class="column"><i class="cloud upload icon"></i>Cloud Upload</div>
				<div class="column"><i class="compress icon"></i>Compress</div>
				<div class="column"><i class="configure icon"></i>Configure</div>
				<div class="column"><i class="download icon"></i>Download</div>
				<div class="column"><i class="edit icon"></i>Edit</div>
				<div class="column"><i class="erase icon"></i>Erase</div>
				<div class="column"><i class="exchange icon"></i>Exchange</div>
				<div class="column"><i class="external share icon"></i>External Share</div>
				<div class="column"><i class="expand icon"></i>Expand</div>
				<div class="column"><i class="filter icon"></i>Filter</div>
				<div class="column"><i class="flag icon"></i>Flag</div>
				<div class="column"><i class="flag outline icon"></i>Flag Outline</div>
				<div class="column"><i class="forward mail icon"></i>Forward Mail</div>
				<div class="column"><i class="hide icon"></i>Hide</div>
				<div class="column"><i class="in cart icon"></i>In Cart</div>
				<div class="column"><i class="lock icon"></i>Lock</div>
				<div class="column"><i class="pin icon"></i>Pin</div>
				<div class="column"><i class="print icon"></i>Print</div>
				<div class="column"><i class="random icon"></i>Random</div>
				<div class="column"><i class="recycle icon"></i>Recycle</div>
				<div class="column"><i class="refresh icon"></i>Refresh</div>
				<div class="column"><i class="remove bookmark icon"></i>Remove Bookmark</div>
				<div class="column"><i class="remove user icon"></i>Remove User</div>
				<div class="column"><i class="repeat icon"></i>Repeat</div>
				<div class="column"><i class="reply all icon"></i>Reply All</div>
				<div class="column"><i class="reply icon"></i>Reply</div>
				<div class="column"><i class="retweet icon"></i>Retweet</div>
				<div class="column"><i class="send icon"></i>Send</div>
				<div class="column"><i class="send outline icon"></i>Send Outline</div>
				<div class="column"><i class="share alternate icon"></i>Share Alternate</div>
				<div class="column"><i class="share alternate square icon"></i>Share Alternate Square</div>
				<div class="column"><i class="share icon"></i>Share</div>
				<div class="column"><i class="share square icon"></i>Share Square</div>
				<div class="column"><i class="sign in icon"></i>Sign in</div>
				<div class="column"><i class="sign out icon"></i>Sign out</div>
				<div class="column"><i class="theme icon"></i>Theme</div>
				<div class="column"><i class="translate icon"></i>Translate</div>
				<div class="column"><i class="undo icon"></i>Undo</div>
				<div class="column"><i class="unhide icon"></i>Unhide</div>
				<div class="column"><i class="unlock alternate icon"></i>Unlock Alternate</div>
				<div class="column"><i class="unlock icon"></i>Unlock</div>
				<div class="column"><i class="upload icon"></i>Upload</div>
				<div class="column"><i class="wait icon"></i>Wait</div>
				<div class="column"><i class="wizard icon"></i>Wizard</div>
				<div class="column"><i class="write icon"></i>Write</div>
				<div class="column"><i class="write square icon"></i>Write Square</div>
      </div>
			<div class="icon-cat-wrap message" data-cat="message">
        <div class="column"><i class="announcement icon"></i>Announcement</div>
        <div class="column"><i class="birthday icon"></i>Birthday</div>
        <div class="column"><i class="flag icon"></i>Flag</div>
        <div class="column"><i class="help icon"></i>Help</div>
        <div class="column"><i class="help circle icon"></i>Help Circle</div>
        <div class="column"><i class="info icon"></i>Info</div>
        <div class="column"><i class="info circle icon"></i>Info Circle</div>
        <div class="column"><i class="warning icon"></i>Warning</div>
        <div class="column"><i class="warning circle icon"></i>Warning Circle</div>
        <div class="column"><i class="warning sign icon"></i>Warning Sign</div>
      </div>
			<div class="icon-cat-wrap user-types" data-cat="usert">
        <div class="column"><i class="child icon"></i>Child</div>
        <div class="column"><i class="doctor icon"></i>Doctor</div>
        <div class="column"><i class="handicap icon"></i>Handicap</div>
        <div class="column"><i class="spy icon"></i>Spy</div>
        <div class="column"><i class="student icon"></i>Student</div>
        <div class="column"><i class="user icon"></i>User</div>
        <div class="column"><i class="users icon"></i>Users</div>
      </div>
			<div class="icon-cat-wrap gender" data-cat="gender">
				<div class="column"><i class="female icon"></i> Female </div>
        <div class="column"><i class="gay icon"></i> Gay </div>
        <div class="column"><i class="heterosexual icon"></i> Heterosexual </div>
        <div class="column"><i class="intergender icon"></i> Intergender </div>
        <div class="column"><i class="lesbian icon"></i> Lesbian </div>
        <div class="column"><i class="male icon"></i> Male </div>
        <div class="column"><i class="man icon"></i> Man </div>
        <div class="column"><i class="neuter icon"></i> Neuter </div>
        <div class="column"><i class="non binary transgender icon"></i> Non Binary Transgender </div>
        <div class="column"><i class="transgender icon"></i> Transgender </div>
        <div class="column"><i class="other gender icon"></i> Other Gender </div>
        <div class="column"><i class="other gender horizontal icon"></i> Other Gender Horizontal </div>
        <div class="column"><i class="other gender vertical icon"></i> Other Gender Vertical </div>
        <div class="column"><i class="woman icon"></i> Woman </div>
			</div>
			<div class="icon-cat-wrap layout" data-cat="layout">
        <div class="column"><i class="grid layout icon"></i>Grid Layout</div>
        <div class="column"><i class="list layout icon"></i>List Layout</div>
        <div class="column"><i class="block layout icon"></i>Block Layout</div>
        <div class="column"><i class="zoom icon"></i>Zoom</div>
        <div class="column"><i class="zoom out icon"></i>Zoom Out</div>
        <div class="column"><i class="resize vertical icon"></i>Resize Vertical</div>
        <div class="column"><i class="resize horizontal icon"></i>Resize Horizontal</div>
        <div class="column"><i class="maximize icon"></i>Maximize</div>
        <div class="column"><i class="crop icon"></i>Crop</div>
			</div>
			<div class="icon-cat-wrap objects" data-cat="objects">
				<div class="column"><i class="anchor icon"></i>Anchor</div>
        <div class="column"><i class="bar icon"></i>Bar</div>
        <div class="column"><i class="bomb icon"></i>Bomb</div>
        <div class="column"><i class="book icon"></i>Book</div>
        <div class="column"><i class="bullseye icon"></i>Bullseye</div>
        <div class="column"><i class="calculator icon"></i>Calculator</div>
        <div class="column"><i class="checkered flag icon"></i>Checkered Flag</div>
        <div class="column"><i class="cocktail icon"></i>Cocktail</div>
        <div class="column"><i class="diamond icon"></i>Diamond</div>
        <div class="column"><i class="fax icon"></i>Fax</div>
        <div class="column"><i class="fire extinguisher icon"></i>Fire Extinguisher</div>
        <div class="column"><i class="fire icon"></i>Fire</div>
        <div class="column"><i class="gift icon"></i>Gift</div>
        <div class="column"><i class="leaf icon"></i>Leaf</div>
        <div class="column"><i class="legal icon"></i>Legal</div>
        <div class="column"><i class="lemon icon"></i>Lemon</div>
        <div class="column"><i class="life ring icon"></i>Life Ring</div>
        <div class="column"><i class="lightning icon"></i>Lightning</div>
        <div class="column"><i class="magnet icon"></i>Magnet</div>
        <div class="column"><i class="money icon"></i>Money</div>
        <div class="column"><i class="moon icon"></i>Moon</div>
        <div class="column"><i class="plane icon"></i>Plane</div>
        <div class="column"><i class="puzzle icon"></i>Puzzle</div>
        <div class="column"><i class="rain icon"></i>Rain</div>
        <div class="column"><i class="road icon"></i>Road</div>
        <div class="column"><i class="rocket icon"></i>Rocket</div>
        <div class="column"><i class="shipping icon"></i>Shipping</div>
        <div class="column"><i class="soccer icon"></i>Soccer</div>
        <div class="column"><i class="suitcase icon"></i>Suitcase</div>
        <div class="column"><i class="sun icon"></i>Sun</div>
        <div class="column"><i class="travel icon"></i>Travel</div>
        <div class="column"><i class="treatment icon"></i>Treatment</div>
        <div class="column"><i class="world icon"></i>World</div>
			</div>
			<div class="icon-cat-wrap media" data-cat="media">
				<div class="column"><i class="area chart icon"></i>Area Chart</div>
        <div class="column"><i class="bar chart icon"></i>Bar Chart</div>
        <div class="column"><i class="camera retro icon"></i>Camera Retro</div>
        <div class="column"><i class="newspaper icon"></i>Newspaper</div>
        <div class="column"><i class="film icon"></i>Film</div>
        <div class="column"><i class="line chart icon"></i>Line Chart</div>
        <div class="column"><i class="photo icon"></i>Photo</div>
        <div class="column"><i class="pie chart icon"></i>Pie Chart</div>
        <div class="column"><i class="sound icon"></i>Sound</div>
			</div>
			<div class="icon-cat-wrap technologies" data-cat="technologies">
				<div class="column"><i class="barcode icon"></i>Barcode</div>
        <div class="column"><i class="css3 icon"></i>Css3</div>
        <div class="column"><i class="database icon"></i>Database</div>
        <div class="column"><i class="fork icon"></i>Fork</div>
        <div class="column"><i class="html5 icon"></i>Html5</div>
        <div class="column"><i class="openid icon"></i>Openid</div>
        <div class="column"><i class="qrcode icon"></i>Qrcode</div>
        <div class="column"><i class="rss icon"></i>RSS</div>
        <div class="column"><i class="rss square icon"></i>RSS Square</div>
        <div class="column"><i class="server icon"></i>Server</div>
			</div>
			<div class="icon-cat-wrap rating" data-cat="rating">
				<div class="column"><i class="empty heart icon"></i>Empty Heart</div>
        <div class="column"><i class="empty star icon"></i>Empty Star</div>
        <div class="column"><i class="frown icon"></i>Frown</div>
        <div class="column"><i class="heart icon"></i>Heart</div>
        <div class="column"><i class="meh icon"></i>Meh</div>
        <div class="column"><i class="smile icon"></i>Smile</div>
        <div class="column"><i class="star half empty icon"></i>Star Half Empty</div>
        <div class="column"><i class="star half icon"></i>Star Half</div>
        <div class="column"><i class="star icon"></i>Star</div>
        <div class="column"><i class="thumbs down icon"></i>Thumbs Down</div>
        <div class="column"><i class="thumbs outline down icon"></i>Thumbs Outline Down</div>
        <div class="column"><i class="thumbs outline up icon"></i>Thumbs Outline Up</div>
        <div class="column"><i class="thumbs up icon"></i>Thumbs Up</div>
			</div>
			<div class="icon-cat-wrap map" data-cat="map">
				<div class="column"><i class="building icon"></i>Building</div>
        <div class="column"><i class="building outline icon"></i>Building Outline</div>
        <div class="column"><i class="car icon"></i>Car</div>
        <div class="column"><i class="coffee icon"></i>Coffee</div>
        <div class="column"><i class="emergency icon"></i>Emergency</div>
        <div class="column"><i class="first aid icon"></i>First Aid</div>
        <div class="column"><i class="food icon"></i>Food</div>
        <div class="column"><i class="h icon"></i>H</div>
        <div class="column"><i class="hospital icon"></i>Hospital</div>
        <div class="column"><i class="location arrow icon"></i>Location Arrow</div>
        <div class="column"><i class="marker icon"></i>Marker</div>
        <div class="column"><i class="military icon"></i>Military</div>
        <div class="column"><i class="paw icon"></i>Paw</div>
        <div class="column"><i class="space shuttle icon"></i>Space Shuttle</div>
        <div class="column"><i class="spoon icon"></i>Spoon</div>
        <div class="column"><i class="taxi icon"></i>Taxi</div>
        <div class="column"><i class="tree icon"></i>Tree</div>
        <div class="column"><i class="university icon"></i>University</div>
			</div>
			<div class="icon-cat-wrap currency" data-cat="currency">
				<div class="column"><i class="dollar icon"></i>Dollar</div>
        <div class="column"><i class="euro icon"></i>Euro</div>
        <div class="column"><i class="lira icon"></i>Lira</div>
        <div class="column"><i class="pound icon"></i>Pound</div>
        <div class="column"><i class="ruble icon"></i>Ruble</div>
        <div class="column"><i class="rupee icon"></i>Rupee</div>
        <div class="column"><i class="shekel icon"></i>Shekel</div>
        <div class="column"><i class="won icon"></i>Won</div>
        <div class="column"><i class="yen icon"></i>Yen</div>
			</div>
			<div class="icon-cat-wrap payment" data-cat="payment">
				<div class="column"><i class="american express icon"></i>American Express</div>
        <div class="column"><i class="discover icon"></i>Discover</div>
        <div class="column"><i class="google wallet icon"></i>Google Wallet</div>
        <div class="column"><i class="mastercard icon"></i>Mastercard</div>
        <div class="column"><i class="paypal card icon"></i>Paypal Card</div>
        <div class="column"><i class="paypal icon"></i>Paypal</div>
        <div class="column"><i class="stripe icon"></i>Stripe</div>
        <div class="column"><i class="visa icon"></i>Visa</div>
			</div>
			<div class="icon-cat-wrap brands" data-cat="brands">
				<div class="column"><i class="adn icon"></i> Adn </div>
        <div class="column"><i class="android icon"></i> Android </div>
        <div class="column"><i class="angellist icon"></i> Angellist </div>
        <div class="column"><i class="apple icon"></i> Apple </div>
        <div class="column"><i class="behance icon"></i> Behance </div>
        <div class="column"><i class="behance square icon"></i> Behance Square </div>
        <div class="column"><i class="bitbucket icon"></i> Bitbucket </div>
        <div class="column"><i class="bitbucket square icon"></i> Bitbucket Square </div>
        <div class="column"><i class="bitcoin icon"></i> Bitcoin </div>
        <div class="column"><i class="buysellads icon"></i> Buysellads </div>
        <div class="column"><i class="codepen icon"></i> Codepen </div>
        <div class="column"><i class="connectdevelop icon"></i> Connectdevelop </div>
        <div class="column"><i class="dashcube icon"></i> Dashcube </div>
        <div class="column"><i class="delicious icon"></i> Delicious </div>
        <div class="column"><i class="deviantart icon"></i> Deviantart </div>
        <div class="column"><i class="digg icon"></i> Digg </div>
        <div class="column"><i class="dribbble icon"></i> Dribbble </div>
        <div class="column"><i class="dropbox icon"></i> Dropbox </div>
        <div class="column"><i class="drupal icon"></i> Drupal </div>
        <div class="column"><i class="empire icon"></i> Empire </div>
        <div class="column"><i class="facebook icon"></i> Facebook </div>
        <div class="column"><i class="facebook square icon"></i> Facebook Square </div>
        <div class="column"><i class="flickr icon"></i> Flickr </div>
        <div class="column"><i class="forumbee icon"></i> Forumbee </div>
        <div class="column"><i class="foursquare icon"></i> Foursquare </div>
        <div class="column"><i class="git icon"></i> Git </div>
        <div class="column"><i class="git square icon"></i> Git Square </div>
        <div class="column"><i class="github alternate icon"></i> Github Alternate </div>
        <div class="column"><i class="github icon"></i> Github </div>
        <div class="column"><i class="github square icon"></i> Github Square </div>
        <div class="column"><i class="gittip icon"></i> Gittip </div>
        <div class="column"><i class="google icon"></i> Google </div>
        <div class="column"><i class="google plus icon"></i> Google Plus </div>
        <div class="column"><i class="google plus square icon"></i> Google Plus Square </div>
        <div class="column"><i class="hacker news icon"></i> Hacker News </div>
        <div class="column"><i class="instagram icon"></i> Instagram </div>
        <div class="column"><i class="ioxhost icon"></i> Ioxhost </div>
        <div class="column"><i class="joomla icon"></i> Joomla </div>
        <div class="column"><i class="jsfiddle icon"></i> Jsfiddle </div>
        <div class="column"><i class="lastfm icon"></i> Lastfm </div>
        <div class="column"><i class="lastfm square icon"></i> Lastfm Square </div>
        <div class="column"><i class="leanpub icon"></i> Leanpub </div>
        <div class="column"><i class="linkedin icon"></i> Linkedin </div>
        <div class="column"><i class="linkedin square icon"></i> Linkedin Square </div>
        <div class="column"><i class="linux icon"></i> Linux </div>
        <div class="column"><i class="maxcdn icon"></i> Maxcdn </div>
        <div class="column"><i class="meanpath icon"></i> Meanpath </div>
        <div class="column"><i class="medium icon"></i> Medium </div>
        <div class="column"><i class="pagelines icon"></i> Pagelines </div>
        <div class="column"><i class="pied piper alternate icon"></i> Pied Piper Alternate </div>
        <div class="column"><i class="pied piper icon"></i> Pied Piper </div>
        <div class="column"><i class="pinterest icon"></i> Pinterest </div>
        <div class="column"><i class="pinterest square icon"></i> Pinterest Square </div>
        <div class="column"><i class="qq icon"></i> Qq </div>
        <div class="column"><i class="rebel icon"></i> Rebel </div>
        <div class="column"><i class="reddit icon"></i> Reddit </div>
        <div class="column"><i class="reddit square icon"></i> Reddit Square </div>
        <div class="column"><i class="renren icon"></i> Renren </div>
        <div class="column"><i class="sellsy icon"></i> Sellsy </div>
        <div class="column"><i class="shirtsinbulk icon"></i> Shirtsinbulk </div>
        <div class="column"><i class="simplybuilt icon"></i> Simplybuilt </div>
        <div class="column"><i class="skyatlas icon"></i> Skyatlas </div>
        <div class="column"><i class="skype icon"></i> Skype </div>
        <div class="column"><i class="slack icon"></i> Slack </div>
        <div class="column"><i class="slideshare icon"></i> Slideshare </div>
        <div class="column"><i class="soundcloud icon"></i> Soundcloud </div>
        <div class="column"><i class="spotify icon"></i> Spotify </div>
        <div class="column"><i class="stack exchange icon"></i> Stack Exchange </div>
        <div class="column"><i class="stack overflow icon"></i> Stack Overflow </div>
        <div class="column"><i class="steam icon"></i> Steam </div>
        <div class="column"><i class="steam square icon"></i> Steam Square </div>
        <div class="column"><i class="stumbleupon circle icon"></i> Stumbleupon Circle </div>
        <div class="column"><i class="stumbleupon icon"></i> Stumbleupon </div>
        <div class="column"><i class="tencent weibo icon"></i> Tencent Weibo </div>
        <div class="column"><i class="trello icon"></i> Trello </div>
        <div class="column"><i class="tumblr icon"></i> Tumblr </div>
        <div class="column"><i class="tumblr square icon"></i> Tumblr Square </div>
        <div class="column"><i class="twitch icon"></i> Twitch </div>
        <div class="column"><i class="twitter icon"></i> Twitter </div>
        <div class="column"><i class="twitter square icon"></i> Twitter Square </div>
        <div class="column"><i class="viacoin icon"></i> Viacoin </div>
        <div class="column"><i class="vimeo icon"></i> Vimeo </div>
        <div class="column"><i class="vine icon"></i> Vine </div>
        <div class="column"><i class="vk icon"></i> Vk </div>
        <div class="column"><i class="wechat icon"></i> Wechat </div>
        <div class="column"><i class="weibo icon"></i> Weibo </div>
        <div class="column"><i class="whatsapp icon"></i> Whatsapp </div>
        <div class="column"><i class="windows icon"></i> Windows </div>
        <div class="column"><i class="wordpress icon"></i> Wordpress </div>
        <div class="column"><i class="xing icon"></i> Xing </div>
        <div class="column"><i class="xing square icon"></i> Xing Square </div>
        <div class="column"><i class="yahoo icon"></i> Yahoo </div>
        <div class="column"><i class="yelp icon"></i> Yelp </div>
        <div class="column"><i class="youtube icon"></i> Youtube </div>
        <div class="column"><i class="youtube play icon"></i> Youtube Play </div>
        <div class="column"><i class="youtube square icon"></i> Youtube Square </div>
			</div>
		</section>
		<footer>
			<button type="button" class="button button-default" onclick="tb_remove()"><?php esc_html_e('Cancel','ctcore'); ?></button>
			<button id="pick-icon" type="button" class="pick-icon button button-primary"><?php esc_html_e('Select','ctcore'); ?></button>
		</footer>
	</div>
</div>
