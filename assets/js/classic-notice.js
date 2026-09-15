/**
 * Notice scripts for classic themes.
 *
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 */

( () => {
	// Re-position the notice after gallery init.
	const gallery = document.querySelector( '.woocommerce-product-gallery' );

	if ( gallery ) {
		const notice = gallery.nextElementSibling;

		if ( notice?.classList.contains( 'authenticimages-notice' ) ) {
			const moveNotice = () => gallery.append( notice );

			window
				.jQuery( gallery )
				.on( 'wc-product-gallery-after-init', moveNotice );
			moveNotice();
		}
	}
} )();
