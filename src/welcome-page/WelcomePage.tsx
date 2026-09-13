/**
 * Copyright 2026 Adrian Duffell
 * Licensed under the GNU General Public License v2.0 or later.
 *
 *
 * Welcome page component for setting up the premium license (and possibly more
 * things in the future).
 *
 * It has two modes:
 *
 * Welcome mode: The user is entering the license for the first time (or more
 * precisely, when the license is blank).
 *
 * Reset mode: The user is recovering from an invalid license state and is
 * resetting the license key on the site.
 */

import apiFetch from '@wordpress/api-fetch';
import { Button, TextControl } from '@wordpress/components';
import { useRef, useState, createInterpolateElement } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { Link } from '@wordpress/ui';
import { dismiss, undoDismiss } from './dismiss';
import { ValidationMessage } from './ValidationMessage';
import { useLicenseValidation } from './useLicenseValidation';

declare const authenticimagesWelcomePage: {
	licenseName: string;
	licenseStatus: 'none' | 'active' | 'not_found' | 'error' | 'expired';
	productsUrl: string;
};

export function WelcomePage(): JSX.Element {
	const {
		licenseKey,
		validationState,
		canActivate,
		handleLicenseKeyChange: updateLicenseKey,
	} = useLicenseValidation();
	const isResetMode = [ 'not_found', 'error' ].includes(
		authenticimagesWelcomePage.licenseStatus
	);
	const isExpiredMode =
		authenticimagesWelcomePage.licenseStatus === 'expired';
	const isWelcomeMode = [ 'none', 'active' ].includes(
		authenticimagesWelcomePage.licenseStatus
	);
	const [ isLoading, setIsLoading ] = useState( false );
	const [ errorMessage, setErrorMessage ] = useState( '' );
	const [ isSuccess, setIsSuccess ] = useState( false );
	const [ isDismissed, setIsDismissed ] = useState( false );
	const pasted = useRef( false );

	function handleLicenseKeyPaste() {
		pasted.current = true;
	}

	function handleLicenseKeyChange( value: string ) {
		const forceValidation = pasted.current;
		pasted.current = false;
		setErrorMessage( '' );
		updateLicenseKey( value, forceValidation );
	}

	async function handleContinue() {
		if ( ! canActivate ) {
			return;
		}

		setIsLoading( true );
		setErrorMessage( '' );
		let settings: { authenticimages_license_key?: string };
		try {
			settings = await apiFetch< {
				authenticimages_license_key?: string;
			} >( {
				path: '/wp/v2/settings',
				method: 'POST',
				data: { authenticimages_license_key: licenseKey },
			} );
		} catch {
			setErrorMessage(
				__(
					'Unable to apply the license. Please try again.',
					'authenticimages'
				)
			);
			setIsLoading( false );
			return;
		}
		if ( settings.authenticimages_license_key !== licenseKey ) {
			setErrorMessage(
				__(
					'Error activating site. Please try again',
					'authenticimages'
				)
			);
			setIsLoading( false );
			return;
		}

		setIsSuccess( true );
		setIsLoading( false );
	}

	function handleDismiss() {
		dismiss();
		setIsDismissed( true );
	}

	function handleUndoDismiss() {
		undoDismiss();
		setIsDismissed( false );
	}

	if ( isDismissed ) {
		return (
			<div className="authenticimages-welcome-page">
				<h1>{ __( 'Setup dismissed', 'authenticimages' ) }</h1>
				<p className="authenticimages-welcome-page__description">
					{ createInterpolateElement(
						__(
							'Complete setup any time from the setup link on the plugins screen. <learnMore>Learn more</learnMore>',
							'authenticimages'
						),
						{
							learnMore: (
								<Link href="https://authenticimages.zip/help/license-key" />
							),
						}
					) }
				</p>

				<div className="authenticimages-welcome-page__button-row">
					<Button variant="secondary" onClick={ handleUndoDismiss }>
						{ __( 'Undo', 'authenticimages' ) }
					</Button>
				</div>
			</div>
		);
	}

	if ( isSuccess ) {
		return (
			<div className="authenticimages-welcome-page">
				<h1>
					{ isResetMode || isExpiredMode
						? __( 'License activated', 'authenticimages' )
						: __( '🎉 Success!', 'authenticimages' ) }
				</h1>
				<p className="authenticimages-welcome-page__description">
					{ isResetMode || isExpiredMode
						? __(
								'License activated. Your premium license includes plugin updates and email support.',
								'authenticimages'
						  )
						: __(
								'Authentic Images is now set up. Get started by adding the authentic badge to a product.',
								'authenticimages'
						  ) }{ ' ' }
					{ createInterpolateElement(
						isResetMode || isExpiredMode
							? __(
									'<learnMore>Learn more</learnMore>',
									'authenticimages'
							  )
							: __(
									'<learnMore>Learn More</learnMore>',
									'authenticimages'
							  ),
						{
							learnMore: (
								<Link
									href={
										isResetMode || isExpiredMode
											? 'https://authenticimages.zip/help/license'
											: 'https://authenticimages.zip/help/get-started/'
									}
								/>
							),
						}
					) }
				</p>
				{ ! ( isResetMode || isExpiredMode ) && (
					<div className="authenticimages-welcome-page__button-row">
						<Button
							variant="primary"
							href={ authenticimagesWelcomePage.productsUrl }
						>
							{ __( 'Get Started', 'authenticimages' ) }
						</Button>
					</div>
				) }
			</div>
		);
	}

	const validationRole = [ 'invalid', 'expired', 'error' ].includes(
		validationState.status
	)
		? 'alert'
		: 'status';
	return (
		<div className="authenticimages-welcome-page">
			<Button
				variant="link"
				className="authenticimages-welcome-page__dismiss"
				onClick={ handleDismiss }
			>
				{ __( 'Dismiss', 'authenticimages' ) }
			</Button>
			<h1>
				{ isResetMode || isExpiredMode
					? __( 'Authentic Images Setup', 'authenticimages' )
					: __( 'Welcome to Authentic Images', 'authenticimages' ) }
			</h1>

			<p className="authenticimages-welcome-page__description">
				{ isResetMode &&
					__(
						'The license could not be verified on this site. Enter your premium license key to continue.',
						'authenticimages'
					) }
				{ isExpiredMode &&
					authenticimagesWelcomePage.licenseName !== '' &&
					sprintf(
						/* translators: %s: lowercase license name. */
						__(
							'Your %s license has expired. Add a new premium license key to continue.',
							'authenticimages'
						),
						authenticimagesWelcomePage.licenseName.toLocaleLowerCase()
					) }
				{ isExpiredMode &&
					authenticimagesWelcomePage.licenseName === '' &&
					__(
						'Your license has expired. Add a new premium license key to continue.',
						'authenticimages'
					) }
				{ isWelcomeMode &&
					__(
						'Thank you for choosing Authentic Images! Enter your premium license key to begin setup.',
						'authenticimages'
					) }
			</p>

			<div className="authenticimages-welcome-page__license-key-input">
				<TextControl
					label={ __( 'Premium license key', 'authenticimages' ) }
					hideLabelFromVision={ true }
					value={ licenseKey }
					onChange={ handleLicenseKeyChange }
					onPaste={ handleLicenseKeyPaste }
					disabled={ isLoading }
					autoComplete="off"
					spellCheck={ false }
					autoCorrect="off"
					autoCapitalize="off"
					__next40pxDefaultSize
				/>
			</div>
			<p
				className={ `authenticimages-welcome-page__validation authenticimages-welcome-page__validation--${ validationState.status }` }
				role={ validationRole }
				aria-live="polite"
			>
				<span key={ validationState.status }>
					<ValidationMessage validationState={ validationState } />
				</span>
			</p>
			<p className="authenticimages-welcome-page__notice">
				{ createInterpolateElement(
					__(
						'By continuing, you agree to the <tos>terms of service</tos> and have read the <privacy>privacy policy</privacy>.',
						'authenticimages'
					),
					{
						tos: (
							<a
								href="https://adrianduffell.com/tos.html"
								target="_blank"
								rel="noopener noreferrer"
							>
								terms of service
							</a>
						),
						privacy: (
							<a
								href="https://adrianduffell.com/privacy.html"
								target="_blank"
								rel="noopener noreferrer"
							>
								privacy policy
							</a>
						),
					}
				) }
			</p>
			<div className="authenticimages-welcome-page__button-row">
				<Button
					variant="primary"
					onClick={ handleContinue }
					isBusy={ isLoading }
					disabled={ isLoading || ! canActivate }
				>
					{ __( 'Activate site', 'authenticimages' ) }
				</Button>
			</div>
			{ errorMessage && (
				<p className="authenticimages-welcome-page__error" role="alert">
					{ errorMessage }
				</p>
			) }
		</div>
	);
}
