/*-----------------------------------------------------------------


-------------------------------------------------------------------
JS TABLE OF CONTENTS
-------------------------------------------------------------------

01. Preloader
02. Scroll to Top
03. magnificPopup
04. Smooth Scrolling
05. Tilt Hover
06. Popup Search
07. Mobile Menu
08. Ajax Form
09. Masonary Function
10. Shape Mockup Function
11. Counter Odometer
12. Progress Line
13. Custom Select
14. Custom Date Select
15. Form Validation
16. Marquee Mode
17. Side Box
18. wow Animation
19.chart Container
20.title Animation
21.Swiper Slider

------------------------------------------------------------------*/

(function ($) {
	"use strict";

	/*===========================================
		=    On Load Function      =
	=============================================*/
	$(window).on("load", function () {
		preLoader();
		// magicCursor();
		smoothScrolling();
		tiltHover();
		popupSearch();
		mobileMenu();
		backgroundImage();
		ajaxForm();
		magnificPopup();
		masonaryFunction();
		shapeMockupFunction();
		counterOdometer();
		progressLine();
		countText();
		customSelect();
		customDateSelect();
		scrollDown();
		formValidation();
		marqueeMode();
		circleBoxAnimation();
		currentBox();
		sideBox();
		masonryIsotope();
		commonJs();
		wowAnimation();
		accordionBox();
		titleAnimation();
		loadMore();
		allSlider();
		gsapController();
		dataItemHover();

	});

	scrollTop();



	/*===========================================
		=    Preloader      =
	=============================================*/
	function preLoader() {
		// Remove the loading screen once the window has fully loaded
		$("#loading-screen").fadeOut(500, function () {
			$(this).remove();
		});
		// Optional: Close loading screen when the close button is clicked
		$(".preloader-close").on("click", function () {
			$(".loading-screen").fadeOut(500, function () {
				$(this).remove();
			});
		});
	}








	/*===========================================
	=         Mobile Menu Active         =
	=============================================*/
	function mobileMenu() {
		if ($(".mobile-menu").length) {
			var mobileMenuContent = $(".tv-header .main-menu .navigation").html();

			$(".mobile-menu .navigation").append(mobileMenuContent);
			$('.sticky-header .navigation').append(mobileMenuContent);
			$.fn.mobilemenu = function (options) {
				var opt = $.extend({
					menuToggleBtn: ".menu-toggle",
					bodyToggleClass: "body-visible",
					subMenuClass: "submenu-class",
					subMenuParent: "submenu-item-has-children",
					subMenuParentToggle: "active-class",
					meanExpandClass: "mean-expand-class",
					appendElement: '<span class="mean-expand-class"></span>',
					subMenuToggleClass: "menu-open",
					toggleSpeed: 400,
				},
					options
				);

				return this.each(function () {
					var menu = $(this);

					function menuToggle() {
						menu.toggleClass(opt.bodyToggleClass);

						var subMenu = "." + opt.subMenuClass;
						$(subMenu).each(function () {
							if ($(this).hasClass(opt.subMenuToggleClass)) {
								$(this).removeClass(opt.subMenuToggleClass);
								$(this).css("display", "none");
								$(this).parent().removeClass(opt.subMenuParentToggle);
							}
						});
					}

					menu.find("li").each(function () {
						var submenu = $(this).find("ul,.tv-mega-menu");
						submenu.addClass(opt.subMenuClass);
						submenu.css("display", "none");
						submenu.parent().addClass(opt.subMenuParent);
						submenu.prev("a").append(opt.appendElement);
						submenu.next("a").append(opt.appendElement);
					});

					function toggleDropDown($element) {
						var $parent = $($element).parent();
						var $siblings = $parent.siblings();

						$siblings.removeClass(opt.subMenuParentToggle);
						$siblings.find("ul,.tv-mega-menu").slideUp(opt.toggleSpeed).removeClass(opt.subMenuToggleClass);

						$parent.toggleClass(opt.subMenuParentToggle);
						$($element).next("ul,.tv-mega-menu").slideToggle(opt.toggleSpeed).toggleClass(opt.subMenuToggleClass);
					}

					var expandToggler = "." + opt.meanExpandClass;
					$(expandToggler).each(function () {
						$(this).on("click", function (e) {
							e.preventDefault();
							toggleDropDown($(this).parent());
						});
					});

					$(opt.menuToggleBtn).each(function () {
						$(this).on("click", function () {
							menuToggle();
						});
					});

					menu.on("click", function (e) {
						e.stopPropagation();
						menuToggle();
					});

					menu.find("div").on("click", function (e) {
						e.stopPropagation();
					});
				});
			};
			$(".mobile-menu-wrapper").mobilemenu();
		}
	}

	/*===========================================
	=         Sticky Fix         =
	=============================================*/
	$(window).scroll(function () {
		var topPos = $(this).scrollTop();
		if (topPos > 100) {
			$('.sticky-header').addClass('fixed-header animated slideInDown');
		} else {
			$('.sticky-header').removeClass('fixed-header animated slideInDown');
		}
	});



	/*===========================================
	=         Scroll To Top         =
	=============================================*/
	function scrollTop() {

		document.addEventListener('DOMContentLoaded', () => {
			const box = document.querySelector(".scrollToTop"),
				water = box?.querySelector(".water");

			if (!box || !water) return;

			const updateScroll = ({ scroll }) => {
				let percent = Math.min((scroll / (document.documentElement.scrollHeight - window.innerHeight)) * 100, 100);
				water.style.transform = `translateY(${100 - percent}%)`;
				box.style.display = scroll >= 200 ? 'block' : 'none';
			};

			const lenis = new Lenis({ lerp: 0.1, smoothWheel: true });
			lenis.on('scroll', updateScroll);

			box.addEventListener('click', () => lenis.scrollTo(0, { duration: 1.5, easing: (t) => 1 - Math.pow(2, -10 * t) }));

			function raf(time) {
				lenis.raf(time);
				requestAnimationFrame(raf);
			}
			requestAnimationFrame(raf);
		});

	}





	/*===========================================
	=         Magic Cursor         =
	=============================================*/
	function magicCursor() {
		// Add the custom cursor element to the body
		$("body").append('<div class="magic-cursor"></div>');

		var cursor = $(".magic-cursor");

		// Update cursor position on mouse move
		$(window).on("mousemove", function (e) {
			cursor.css({
				transform: "translate(" + (e.clientX - 15) + "px," + (e.clientY - 15) + "px)",
				visibility: "inherit"
			});
		});

		// Handle hover states for links and buttons
		$("a, button, .theme-button, .scroll-top, .acc-btn ").on("mouseenter", function () {
			cursor.addClass("cursor-grow");
		});

		$("a, button, .theme-button, .scroll-top, .acc-btn").on("mouseleave", function () {
			cursor.removeClass("cursor-grow");
		});
	}

	/*===========================================
	=    Initialize Lenis / Smooth Scrolling    =
	=============================================*/
	function smoothScrolling() {
		const lenis = new Lenis();

		lenis.on('scroll', (e) => {
			// console.log(e);
		});

		function raf(time) {
			lenis.raf(time);
			requestAnimationFrame(raf);
		}

		requestAnimationFrame(raf);
	}


	/*===========================================
	=         Custom Tilt Js Start        =
	=============================================*/
	function tiltHover() {
		const tilt = document.querySelectorAll(".tilt");
		VanillaTilt.init(tilt, {
			reverse: true,
			max: 15,
			speed: 400,
			scale: 1.01,
			glare: true,
			reset: true,
			perspective: 800,
			transition: true,
			"max-glare": 0.45,
			"glare-prerender": false,
			gyroscope: true,
			gyroscopeMinAngleX: -45,
			gyroscopeMaxAngleX: 45,
			gyroscopeMinAngleY: -45,
			gyroscopeMaxAngleY: 45,
		});
	}


	/*===========================================
	=         PopUp Search         =
	=============================================*/
	function popupSearch() {
		if ($(".search-btn").length) {
			$(".search-btn").click(function () {
				$("body").addClass("search-active");
			}).end().find(".close-search").click(function () {
				$("body").removeClass("search-active");
			});
		}
	}

	/*===========================================
	=         Set Background Image         =
	=============================================*/
	function backgroundImage() {
		if ($("[data-bg-src]").length > 0) {
			$("[data-bg-src]").each(function () {
				var src = $(this).attr("data-bg-src");
				$(this).css("background-image", "url(" + src + ")");
				$(this).removeAttr("data-bg-src").addClass("background-image");
			});
		}
	}


	/*===========================================
	=         Ajax Contact Form         =
	=============================================*/
	function ajaxForm() {
		const handleFormSubmission = (form, formIndex) => {
			form.addEventListener("submit", ev => {
				ev.preventDefault();
				const data = new FormData(form);
				const submitButton = form.querySelector('button[type="submit"]');
				const originalButtonText = submitButton.querySelector('.btn-title').textContent;
				const loadingText = submitButton.dataset.loadingText;
				submitButton.querySelector('.btn-title').textContent = loadingText;
				submitButton.disabled = true;

				sendAjaxRequest("POST", form.action, data,
					response => handleSuccess(response, form, submitButton, originalButtonText),
					(statusCode, responseText) => handleError(statusCode, responseText, form, submitButton, originalButtonText)
				);
			});
		};

		const handleSuccess = (response, form, submitButton, originalButtonText) => {
			form.reset();
			let message = 'Success!';
			if (form.classList.contains('appointment-form')) {
				message = 'Appointment booked successfully!';
			} else if (form.classList.contains('contact_form')) {
				message = 'Contact submitted!';
			} else if (form.classList.contains('consulation-form')) {
				message = 'Consulation Submited!';
			} else if (form.classList.contains('comment-form')) {
				message = 'Comment Done!';
			} else if (form.classList.contains('subscribe-form')) {
				message = 'Subscribed!';
			} else if (form.classList.contains('newsletter-form')) {
				message = 'Subscribed!';
			}
			showPopup('success', message);
			submitButton.querySelector('.btn-title').textContent = originalButtonText;
			submitButton.disabled = false;
		};

		const handleError = (statusCode, responseText, form, submitButton, originalButtonText) => {
			let message = 'Oops! There was a problem.';
			if (form.classList.contains('appointment-form')) {
				message = 'Failed to book the appointment. Please try again.';
			} else if (form.classList.contains('comment-form')) {
				message = 'Somthing Mistake!';
			}
			showPopup('error', message);
			submitButton.querySelector('.btn-title').textContent = originalButtonText;
			submitButton.disabled = false;
		};

		const sendAjaxRequest = (method, url, data, successCallback, errorCallback) => {
			const xhr = new XMLHttpRequest();
			xhr.open(method, url);
			xhr.setRequestHeader("Accept", "application/json");
			xhr.onreadystatechange = () => {
				if (xhr.readyState !== XMLHttpRequest.DONE) return;
				if (xhr.status === 200) {
					successCallback(xhr.response);
				} else {
					errorCallback(xhr.status, xhr.responseText);
				}
			};
			xhr.send(data);
		};

		const showPopup = (status, message) => {
			const popup = document.createElement('div');
			popup.className = `popup-status ${status}`;
			popup.innerHTML = `<i class="far fa-${status === 'success' ? 'check-circle' : 'times-circle'}"></i> ${message}`;
			document.body.appendChild(popup);
			setTimeout(() => popup.remove(), 3000); // Remove the popup after 3 seconds
		};

		const forms = document.querySelectorAll(".appointment-form, .contact_form, .consulation-form, .comment-form, .subscribe-form, .newsletter-form");

		forms.forEach((form, index) => handleFormSubmission(form, index));
	}


	/*===========================================
	=         Magnific Popup         =
	=============================================*/
	function magnificPopup() {
		//Fancybox
		Fancybox.bind("[data-fancybox]", {
			animated: true,
			transitionEffect: "fade",
		});


	}


	/*===========================================
	=        Masonary Active         =
	=============================================*/
	function masonaryFunction() {
		const elem = document.querySelector('.masonary-active');
		if (elem) {
			imagesLoaded(elem, () => {
				const iso = new Isotope(elem, {
					itemSelector: '.filter-item',
					layoutMode: 'fitRows'
				});

				document.querySelector('.portfolio-filter').addEventListener('click', (e) => {
					if (e.target.matches('li')) {
						iso.arrange({
							filter: e.target.getAttribute('data-filter')
						});
						document.querySelector('.current_menu_item')?.classList.remove('current_menu_item');
						e.target.classList.add('current_menu_item');
					}
				});
			});
		}

	}


	/*===========================================
	=         Shape Mockup         =
	=============================================*/
	function shapeMockupFunction() {
		$.fn.shapeMockup = function () {
			var $shape = $(this);
			$shape.each(function () {
				var $currentShape = $(this),
					shapeTop = $currentShape.data("top"),
					shapeRight = $currentShape.data("right"),
					shapeBottom = $currentShape.data("bottom"),
					shapeLeft = $currentShape.data("left");
				$currentShape
					.css({
						top: shapeTop,
						right: shapeRight,
						bottom: shapeBottom,
						left: shapeLeft,
					})
					.removeAttr("data-top")
					.removeAttr("data-right")
					.removeAttr("data-bottom")
					.removeAttr("data-left")
					.parent()
					.addClass("shape-mockup-wrap");
			});
		};

		if ($(".shape-mockup")) {
			$(".shape-mockup").shapeMockup();
		}
	}


	/*===========================================
	=         Counter Up Odometer         =
	=============================================*/
	function counterOdometer() {
		if ($('.count-number').length) {
			$('.count-number').appear(function () {
				var odo = $(this);
				var countNumber = odo.attr("data-count");

				odo.html(countNumber);

				setTimeout(function () {
					odo.html(countNumber);
				}, 500);
			}, {
				accY: 0
			});
		}
	}


	/*===========================================
	=         Skills progressLine         =
	=============================================*/
	function progressLine() {
		if ($('.progress-line').length) {
			$('.progress-line').appear(function () {
				let el = $(this);
				let percent = el.data('width');
				el.css('width', percent + '%');
			}, {
				accY: 0
			});
		}
	}

	/*===========================================
	=      Progress Counter + Text Count        =
	=============================================*/
	function countText() {
		if ($('.count-box').length) {
			$(".count-box").appear(
				function () {
					let $t = $(this),
						n = $t.find(".count-text").attr("data-stop"),
						r = parseInt($t.find(".count-text").attr("data-speed"), 10);

					if (!$t.hasClass("counted")) {
						$t.addClass("counted");
						$({
							countNum: $t.find(".count-text").text()
						}).animate({
							countNum: n,
						}, {
							duration: r,
							easing: "linear",
							step: function () {
								$t.find(".count-text").text(Math.floor(this.countNum));
							},
							complete: function () {
								$t.find(".count-text").text(this.countNum);
							},
						});
					}
				}, {
				accY: 0
			}
			);
		}
	}

	/*===========================================
	=        Select2 Active         =
	=============================================*/
	function customSelect() {
		$('.custom-select').select2({
			minimumResultsForSearch: 4,
		});
	}


	/*===========================================
	=        Custom Date & Time Picker         =
	=============================================*/
	function customDateSelect() {
		if ($(".datepicker").length) {
			$(".datepicker").datepicker({
				dateFormat: "mm/dd/yy",
				showAnim: "slideDown",
				changeMonth: true,
				changeYear: true
			});
		}
	}


	/*===========================================
	=        Scroll Down        =
	=============================================*/
	function scrollDown() {
		const scrollLink = document.getElementById("scrollLink");
		if (scrollLink) {
			scrollLink.addEventListener("click", function (event) {
				event.preventDefault();

				const targetSection = document.querySelector(this.getAttribute("href"));

				if (targetSection) {
					window.scrollTo({
						top: targetSection.offsetTop,
						behavior: "smooth"
					});
				}
			});
		}
	}


	/* =======================
	Form Validation
	======================= */
	function formValidation() {
		if ($("#appointment_form,#contact_form,#comment-form").length) {
			$("#appointment_form,#contact_form,#comment-form").validate({
				rules: {
					name: "required",
					email: {
						required: true,
						email: true
					},
					message: "required"
				},
				messages: {
					name: "Please enter your name",
					email: {
						required: "Please enter your email address",
						email: "Please enter a valid email address" // Modify this message as needed
					},
					message: "Please enter a message"
				}
				// Other options if needed
			});
		}
	}


	/*===========================================
	=         Marquee Active         =
	=============================================*/
	function marqueeMode() {
		if ($(".marquee_mode").length) {
			$('.marquee_mode').marquee({
				speed: 40,
				gap: 0,
				delayBeforeStart: 0,
				direction: 'left',
				duplicated: true,
				pauseOnHover: true,
				startVisible: true,
			});
		}
	}


	// Circle Box  Animation
	function circleBoxAnimation() {
		function createTextAnimation(textElement, circleBoxElement) {
			textElement.style.cssText = "animation: text-rotate 10s linear infinite;";

			const textRotateAnimation = textElement.animate(
				[{
					transform: "rotate(0deg)"
				}, {
					transform: "rotate(360deg)"
				}], {
				duration: 10000,
				iterations: Infinity,
				easing: "linear",
			}
			);

			circleBoxElement.addEventListener("mouseenter", () => textRotateAnimation.pause());
			circleBoxElement.addEventListener("mouseleave", () => textRotateAnimation.play());
		}

		// Apply text animation to elements with the class "circle-box"
		const circleBoxes = document.querySelectorAll(".circle-box");
		circleBoxes.forEach(circleBox => {
			const text = circleBox.querySelector(".text-inner");
			createTextAnimation(text, circleBox);
		});

		const chooseUsShapes = document.querySelectorAll(".section-shape > div > img");
		chooseUsShapes.forEach(chooseUsShape => {
			createTextAnimation(chooseUsShape, chooseUsShape.parentElement);
		});
	}


	// Current active Box
	function currentBox() {
		// Process box current
		if ($(".process-single-box").length) {
			$('.process-single-box').on('mouseenter', function () {
				$(this).addClass('current');
				$('.process-single-box').not(this).removeClass('current');
			});
		}

		// service-box-four current  use this project home4
		if ($(".service-box-four").length) {
			$('.service-box-four').on('mouseenter', function () {
				$(this).addClass('current');
				$('.service-box-four').not(this).removeClass('current');
			});
		}


		// testimonial itam current use this project home4
		if ($('.simple-testi-box').length) {
			$('.simple-testi-box').on('mouseenter', function () {
				$(this).addClass('active');
				$('.simple-testi-box').not(this).removeClass('active');
			});
		}


		// team box current
		if ($(".team-box-one").length) {
			$('.team-box-one').on('mouseenter', function () {
				$(this).addClass('current');
				$('.team-box-one').not(this).removeClass('current');
			});
		}

		// pricing box current
		if ($('.pricing-card').length) {
			$('.pricing-card').on('mouseenter', function () {
				$(this).addClass('active');
				$('.pricing-card').not(this).removeClass('active');
			});
		}


		// Project Item current
		if ($('.project-item-box').length) {
			$('.project-item-box').on('mouseenter', function () {
				$(this).addClass('active');
				$('.project-item-box').not(this).removeClass('active');
			});
		}

		if ($('.sidebar-service-list').length) {
			$('.sidebar-service-list ul li').on('mouseenter', function () {
				$(this).addClass('current');
				$('.sidebar-service-list ul li').not(this).removeClass('current');
			});
		}

	}

	if ($('.choose-tab-wrapper').length) {
		const tabButtons = document.querySelectorAll('.choose-card');
		const tabContents = document.querySelectorAll('.choose-tab-wrapper');

		tabButtons.forEach((button, index) => {
			button.addEventListener('click', () => {
				// Remove active class from all buttons and contents
				tabButtons.forEach(btn => btn.classList.remove('active'));
				tabContents.forEach(content => content.classList.remove('active'));

				// Add active class to clicked button
				button.classList.add('active');

				// Add active class to corresponding content
				const activeContent = tabContents[index];
				activeContent.classList.add('active');
			});
		});
	}

	// Project Slider
	if ($('#billingSwitch').length) {
		const billingSwitch = document.getElementById("billingSwitch");
		const monthlyPlans = document.getElementById("monthly");
		const yearlyPlans = document.getElementById("yearly");

		// Initialize state
		monthlyPlans.classList.add("active");
		yearlyPlans.classList.remove("active");

		// Toggle function
		billingSwitch.addEventListener("click", function () {
			const isYearlyActive = yearlyPlans.classList.contains("active");

			if (isYearlyActive) {
				yearlyPlans.classList.remove("active");
				monthlyPlans.classList.add("active");
			} else {
				yearlyPlans.classList.add("active");
				monthlyPlans.classList.remove("active");
			}

			// Add toggle animation to switch button
			billingSwitch.classList.add("toggled");
			setTimeout(() => billingSwitch.classList.remove("toggled"), 300);
		});

		// Toggle the 'active' class on click
		billingSwitch.addEventListener("click", function () {
			billingSwitch.classList.toggle("active");
		});
	}




	/*===========================================
	=         Popup Sidebox         =
	=============================================*/
	function sideBox() {
		$("body").removeClass("open-sidebar");
		$(document).on("click", ".sidebar-trigger", function (e) {
			e.preventDefault();
			$("body").toggleClass("open-sidebar");
		});
		$(document).on("click", ".sidebar-close-btn, .sidebar-overlay", function (e) {
			e.preventDefault();
			$("body.open-sidebar").removeClass("open-sidebar");
		});
	}


	/*===========================================
	=         Masonary Isotope         =
	=============================================*/
	function masonryIsotope() {
		if ($(".image_load").length) {
			$('.image_load').imagesLoaded(function () {

				if ($.fn.isotope) {

					var $portfolio = $('.image_load'); // Fixed selector typo

					$portfolio.isotope({

						itemSelector: '.grid-item',
						filter: '*',
						layoutMode: 'masonry',
						transitionDuration: '0.8s'
					});

					// Menu filtering functionality
					$('.menu-filtering li').on('click', function () {

						$('.menu-filtering li').removeClass('active');
						$(this).addClass('active');

						var selector = $(this).attr('data-filter');
						$portfolio.isotope({
							filter: selector
						});
					});
				}
			});
		}
	}


	/*===========================================
	=         Common Js         =
	=============================================*/
	function commonJs() {
		$("[data-background").each(function () {
			$(this).css("background-image", "url( " + $(this).attr("data-background") + "  )");
		});

		$("[data-cw]").each(function () {
			$(this).css("width", $(this).attr("data-cw"));
		});

		$("[data-left], [data-right], [data-top], [data-bottom]").each(function () {
			const directions = ["left", "right", "top", "bottom"];
			directions.forEach(direction => {
				const value = $(this).attr(`data-${direction}`);
				if (value) {
					$(this).css(direction, value); // Apply inline CSS
					$(this).removeAttr(`data-${direction}`); // Remove the attribute
				}
			});
		});

		$("[data-z-index]").each(function () {
			const value = $(this).attr("data-z-index");
			if (value) {
				$(this).css("z-index", value); // Apply the z-index as inline CSS
				$(this).removeAttr("data-z-index"); // Remove the data-z-index attribute
			}
		});



		$("[data-bg-color]").each(function () {
			$(this).css("background-color", $(this).attr("data-bg-color"));
		});

		$("[data-text-color]").each(function () {
			$(this).css("color", $(this).attr("data-text-color"));
		});

		$(".has-img").each(function () {
			var imgSrc = $(this).attr("data-menu-img");
			var img = `<img class="mega-menu-img" src="${imgSrc}" alt="img">`;
			$(this).append(img);

		});
	}


	/*===========================================
		=        Wow Active         =
	=============================================*/
	function wowAnimation() {
		if ($(".wow").length) {
			var wow = new WOW({
				boxClass: "wow",
				animateClass: "animated",
				mobile: false,
				live: true,
			});
			wow.init();
		}
	}

	/*===========================================
		=        Aos Active         =
	=============================================*/
	// AOS.init();


	/*===========================================
		=        Accordion Box         =
	=============================================*/
	function accordionBox() {
		if ($('.accordion-box').length) {
			$(".accordion-box").on('click', '.acc-btn', function () {

				var outerBox = $(this).parents('.accordion-box');
				var target = $(this).parents('.accordion');

				if ($(this).hasClass('active') !== true) {
					$(outerBox).find('.accordion .acc-btn').removeClass('active ');
				}

				if ($(this).next('.acc-content').is(':visible')) {
					return false;
				} else {
					$(this).addClass('active');
					$(outerBox).children('.accordion').removeClass('active-block');
					$(outerBox).find('.accordion').children('.acc-content').slideUp(300);
					target.addClass('active-block');
					$(this).next('.acc-content').slideDown(300);
				}
			});
		}
	}



	/*===========================================
		=        Title Animation         =
	=============================================*/
	function titleAnimation() {
		if (window.innerWidth < 767) return;

		const animatedTitles = document.querySelectorAll('.sec-title, .hero-content .title, .title-anim');

		const setInitialStyles = (chars, animationType) => {
			gsap.set(chars, {
				opacity: 0,
				display: 'inline-block',
				transformOrigin: 'center center',
				...(() => {
					switch (animationType) {
						case 'slide-down': return { y: -20 };
						case 'rotate': return { rotation: -90 };
						case 'zoom-in': return { scale: 0 };
						case 'fade-up': return { y: 20 };
						case 'bounce-in': return { scale: 0.5 };
						case 'flip': return { rotationY: 90 };
						default: return { x: 20 };
					}
				})()
			});
		};

		const animateChars = (chars, animationType) => {
			gsap.to(chars, {
				opacity: 1,
				duration: 0.8,
				stagger: 0.03,
				ease: "back.out(1.7)",
				...(() => {
					switch (animationType) {
						case 'slide-down': return { y: 0 };
						case 'rotate': return { rotation: 0 };
						case 'zoom-in': return { scale: 1 };
						case 'fade-up': return { y: 0 };
						case 'bounce-in': return { scale: 1 };
						case 'flip': return { rotationY: 0 };
						default: return { x: 0 };
					}
				})()
			});
		};

		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const el = entry.target;
					const animationType = el.getAttribute('data-animation') || 'slide-right';
					const split = new SplitType(el, { types: 'chars' });
					setInitialStyles(split.chars, animationType);
					animateChars(split.chars, animationType);
					observer.unobserve(el); // Run once
				}
			});
		}, { threshold: 0.2 });

		animatedTitles.forEach(el => observer.observe(el));
	}



	/*===========================================
		=        Item Load More        =
	=============================================*/
	function loadMore() {
		if ($('#loadMore').length) {
			$('#loadMore').on('click', function () {
				const loadMoreButton = $(this);
				const originalButtonText = loadMoreButton.find('.btn-title').html();
				const loadingText = loadMoreButton.data('loading-text');

				loadMoreButton.find('.btn-title').html(loadingText);
				loadMoreButton.addClass('i-none');

				$('#loader').show();

				setTimeout(function () {
					$('#loader').hide();

					$('.more-items:hidden').slice(0, 3).slideDown();

					loadMoreButton.find('.btn-title').html(originalButtonText);
					loadMoreButton.removeClass('i-none');



				}, 1500);
			});
		}
	}


	/*===========================================
		=        All Slider        =
	=============================================*/
	function allSlider() {

		// Hero Slider
		if ($('.hero-slider1').length) {
			new Swiper(".hero-slider1", {
				loop: true,
				speed: 800,
				autoplay: {
					delay: 5000,
					disableOnInteraction: false,
				},
				effect: 'cube',
				cubeEffect: {
					shadow: true,
					slideShadows: true,
					shadowOffset: 20,
					shadowScale: 0.94,
				},
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				pagination: false,
				slidesPerView: 1
			});
		}

		// Brands Slider

		if ($('.brands-slider').length) {
			new Swiper(".brands-slider", {
				spaceBetween: 20,
				speed: 1500,
				loop: true,
				autoplay: {
					delay: 7000,
					disableOnInteraction: false,
				},
				pagination: false,
				breakpoints: {
					1199: {
						slidesPerView: 6,
					},
					854: {
						slidesPerView: 4,
					},
					767: {
						slidesPerView: 3,
					},
					540: {
						slidesPerView: 2,
					},
					0: {
						slidesPerView: 1,
					}
				},
			});
		}

		// Brands Slider use this project Home page 3
		if ($(".brands-slider-three").length > 0) {
			const brands = new Swiper(".brands-slider-three", {
				slidesPerView: "auto",
				spaceBetween: 20,
				centeredSlides: true,
				loop: true,
				speed: 5000,
				allowTouchMove: true,
				freeMode: {
					enabled: true,
					momentum: false,
				},
				autoplay: {
					delay: 0,
					disableOnInteraction: false,
					pauseOnMouseEnter: false,
				},
				breakpoints: {
					1199: { slidesPerView: 6 },
					854: { slidesPerView: 4 },
					767: { slidesPerView: 3 },
					540: { slidesPerView: 2 },
					0: { slidesPerView: 1 },
				},
				on: {
					init(swiper) {
						swiper.wrapperEl.style.transitionTimingFunction = "linear";
					},
				},
			});

			//  Swiper autoplay restart on any pointer/click event
			const sliderEl = document.querySelector(".brands-slider-three");
			["mousedown", "mouseup", "touchstart", "touchend", "click"].forEach(evt => {
				sliderEl.addEventListener(evt, () => {
					setTimeout(() => {
						if (!brands.autoplay.running) {
							brands.autoplay.start();
						}
					}, 100);
				});
			});

			// ✅ Extra failsafe: autoplay বন্ধ হলে ৩ সেকেন্ড পর আবার চালাও
			setInterval(() => {
				if (!brands.autoplay.running) {
					brands.autoplay.start();
				}
			}, 3000);
		}

		// Brands Slider Two
		if ($('.brands-slider-two').length) {
			new Swiper(".brands-slider-two", {
				spaceBetween: 30,
				speed: 1500,
				loop: true,
				autoplay: {
					delay: 7000,
					disableOnInteraction: false,
				},
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					1200: {
						slidesPerView: 5,
					},
					992: {
						slidesPerView: 5,
					},
					767: {
						slidesPerView: 4,
					},
					575: {
						slidesPerView: 3,
					},
					0: {
						slidesPerView: 2,
					}
				},
			});
		}





		// Testimonial Slider   -------use this project home 1 --------
		if ($('.testi-slider').length) {
			new Swiper(".testi-slider", {
				spaceBetween: 24,
				speed: 1500,
				loop: true,
				autoplay: false,
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					1024: {
						slidesPerView: 3,
					},
					768: {
						slidesPerView: 2,
					},
					576: {
						slidesPerView: 2,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}


		// Testimonial Slider Two   use this project home2
		if ($('.testi-slider-two').length) {
			new Swiper(".testi-slider-two", {
				spaceBetween: 30,
				speed: 1500,
				loop: true,
				autoplay: {
					delay: 3000,
					disableOnInteraction: false,
				},
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					992: {
						slidesPerView: 1,
					},
					912: {
						slidesPerView: 2,
					},
					575: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		// Testimonial Slider Three   use this project home3
		if ($('.testi-slider-three').length) {
			new Swiper(".testi-slider-three", {
				spaceBetween: 30,
				speed: 1500,
				loop: true,
				autoplay: {
					delay: 3000,
					disableOnInteraction: false,
				},
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					1199: {
						slidesPerView: 2,
					},
					1024: {
						slidesPerView: 1,
					},
					992: {
						slidesPerView: 1,
					},
					575: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		// Testimonial Slider Four   use this project home7
		if ($('.testi-slider-four').length) {
			new Swiper(".testi-slider-four", {
				spaceBetween: 30,
				speed: 1500,
				loop: true,
				autoplay: {
					delay: 3000,
					disableOnInteraction: false,
				},
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					767: {
						slidesPerView: 2,
					},
					575: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}



		// Team Slider
		if ($('.team-slider').length) {
			new Swiper(".team-slider", {
				spaceBetween: 24,
				speed: 1500,
				loop: true,
				autoplay: false,
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					992: {
						slidesPerView: 3,
					},
					768: {
						slidesPerView: 2,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}

		// Service Slider use this project home2
		if ($('.service-slider').length) {
			new Swiper(".service-slider", {
				spaceBetween: 24,
				speed: 1500,
				loop: true,
				autoplay: false,
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					992: {
						slidesPerView: 3,
					},
					768: {
						slidesPerView: 2,
					},
					576: {
						slidesPerView: 2,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}




		// hero slider Slider   Use this project
		if ($('.hero-slider').length) {
			new Swiper(".hero-slider", {
				spaceBetween: 24,
				speed: 1500,
				effect: 'fade',
				loop: true,
				autoplay: {
					delay: 4000,
					disableOnInteraction: false,
				},
				autoplay: true,
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					1024: {
						slidesPerView: 1,
					},
					768: {
						slidesPerView: 1,
					},
					576: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}


		// hero slider Slider   Use this project home6
		if ($('.hero-slider-2').length) {
			new Swiper(".hero-slider-2", {
				spaceBetween: 24,
				speed: 1500,
				effect: 'fade',
				loop: true,
				autoplay: {
					delay: 4000,
					disableOnInteraction: false,
				},
				autoplay: true,
				pagination: false,
				navigation: {
					prevEl: ".array-prev",
					nextEl: ".array-next",
				},
				breakpoints: {
					1024: {
						slidesPerView: 1,
					},
					768: {
						slidesPerView: 1,
					},
					576: {
						slidesPerView: 1,
					},
					0: {
						slidesPerView: 1,
					},
				},
			});
		}


	}




	// strategy-item  Use this project home2

	if ($(".strategy-item").length) {
		const $image = $("#strategy-img");
		$(".strategy-item").on("mouseenter", function () {
			$(".strategy-item").removeClass("active");
			$(this).addClass("active");

			const newImg = $(this).data("img");

			$image.addClass("fade-out");

			setTimeout(() => {
				$image.attr("src", newImg);
				$image.removeClass("fade-out");
			}, 300);
		});
	}




	// Team Slider One use this project home2
	if ($('.tv-team-slider').length) {
		new Swiper(".tv-team-slider", {
			spaceBetween: 24,
			speed: 1500,
			loop: true,
			autoplay: false,
			pagination: false,
			navigation: {
				prevEl: ".array-prev",
				nextEl: ".array-next",
			},
			breakpoints: {
				1024: {
					slidesPerView: 2,
				},
				768: {
					slidesPerView: 1,
				},
				576: {
					slidesPerView: 2,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});
	}




	// team hover style  use this project 
	if ($('.tv-team-card').length) {

		document.querySelectorAll('.tv-team-card').forEach(card => {
			const border = document.createElement('div');
			border.classList.add('svg-border');
			border.innerHTML = `
		<svg viewBox="0 0 100 100" preserveAspectRatio="none">
			<rect class="rect1" x="0.5" y="0.5" width="99" height="99" rx="6" ry="6" />
			<rect class="rect2" x="0.5" y="0.5" width="99" height="99" rx="6" ry="6" />
		</svg>`;
			card.prepend(border);
		});

	}


	// team hover style  use this project 
	if ($('.bg-thumb').length) {

		document.querySelectorAll('.bg-thumb').forEach(card => {
			const border = document.createElement('div');
			border.classList.add('svg-border');
			border.innerHTML = `
		<svg viewBox="0 0 100 100" preserveAspectRatio="none">
			<rect class="rect1" x="0.5" y="0.5" width="99" height="99" rx="6" ry="6" />
			<rect class="rect2" x="0.5" y="0.5" width="99" height="99" rx="6" ry="6" />
		</svg>`;
			card.prepend(border);
		});

	}



	// project box js use this project home3

	if ($('.tv-project-card').length) {
		$('.tv-project-card').on('mouseenter', function () {
			$(this).addClass('active');
			$('.tv-project-card').not(this).removeClass('active');
		});
	}


	// choose tab js use this project home3

	if ($('.choose-tab-wrapper').length) {
		const tabButtons = document.querySelectorAll('.choose-card');
		const tabContents = document.querySelectorAll('.choose-tab-wrapper');

		tabButtons.forEach((button, index) => {
			button.addEventListener('click', () => {
				if (button.classList.contains('active')) return;

				const currentActiveTab = document.querySelector('.choose-tab-wrapper.active');
				const currentActiveButton = document.querySelector('.choose-card.active');

				if (currentActiveTab) {
					currentActiveTab.classList.remove('active');
				}
				if (currentActiveButton) {
					currentActiveButton.classList.remove('active');
				}

				button.classList.add('active');

				setTimeout(() => {
					tabContents[index].classList.add('active');
				}, 50);
			});
		});
	};



	// choose section use this porject home5
	if ($('.title-box').length) {
		document.querySelectorAll('.title-box').forEach(titleBox => {
			titleBox.addEventListener('mouseenter', function () {
				document.querySelectorAll('.title-box').forEach(t => t.classList.remove('active'));
				document.querySelectorAll('.choose-right img').forEach(img => {
					img.classList.remove('active');
					img.style.opacity = '0';
				});

				this.classList.add('active');
				const targetImg = document.getElementById(this.getAttribute('data-target'));
				targetImg.classList.add('active');
				setTimeout(() => {
					targetImg.style.opacity = '1';
				}, 50);
			});
		});
	}





	// Case Slider
	if ($('.portfolio-slider').length) {
		new Swiper(".portfolio-slider", {
			direction: "horizontal",
			mousewheel: true,
			spaceBetween: 24,
			slideShadows: 3,
			speed: 1500,
			loop: true,
			autoplay: false,
			pagination: {
				el: ".dot",
				clickable: true
			},
			navigation: {
				prevEl: ".array-prev",
				nextEl: ".array-next",
			},
			breakpoints: {
				854: {
					slidesPerView: 2,
				},
				0: {
					slidesPerView: 1,
				},
			},
		});
	}



	// project js use this project home6
	document.addEventListener('DOMContentLoaded', function () {
		const projectBoxes = document.querySelectorAll('.col-lg-3[data-thumb]');
		const thumbs = document.querySelectorAll('.thumb[data-thumb]');
		const container = document.querySelector('.row.gx-0.position-relative');

		// Initialize first thumb as active
		thumbs.forEach(thumb => thumb.classList.remove('active'));
		document.querySelector('.thumb[data-thumb="1"]').classList.add('active');

		// Hover handler
		function handleHover(thumbNumber) {
			thumbs.forEach(thumb => {
				thumb.classList.remove('active');
				if (thumb.dataset.thumb === thumbNumber) {
					thumb.classList.add('active');
				}
			});
		}

		// Project box hover
		projectBoxes.forEach(box => {
			box.addEventListener('mouseenter', function () {
				handleHover(this.dataset.thumb);
			});
		});

		// Reset to first thumb when leaving container
		container.addEventListener('mouseleave', function () {
			thumbs.forEach(thumb => thumb.classList.remove('active'));
			document.querySelector('.thumb[data-thumb="1"]').classList.add('active');
		});
	});




	/* ------------- Gsap registration Js ------------- */
	gsap.registerPlugin(ScrollTrigger, ScrollSmoother, ScrollToPlugin);

	if ($("#smooth-wrapper").length && $("#smooth-content").length) {
		gsap.config({
			nullTargetWarn: false,
		});

		let smoother = ScrollSmoother.create({
			smooth: 1.5,
			effects: true,
			smoothTouch: 0.1,
			ignoreMobileResize: true,
		});
	}


	if ($('.from-left').length) {
		// Left Column Animation
		gsap.from(".from-left", {
			x: -200,
			opacity: 0,
			duration: 1,
			scrollTrigger: {
				trigger: ".from-left",
				start: "top 80%",
				scrub: true,
				markers: false
			}
		});
	}

	if ($('.from-right').length) {
		// Right Column Animation
		gsap.from(".from-right", {
			x: 200,
			opacity: 0,
			duration: 1,
			scrollTrigger: {
				trigger: ".from-right",
				start: "top 80%",
				scrub: true,
				markers: false
			}
		});
	}



	// হোম ৪ এর ইমেজ স্কলিং এর জন্য কিন্তু কাজ করে নাই পরে দেখবো
	document.addEventListener("scroll", function () {
		const images = document.querySelectorAll("[data-speed]");

		images.forEach(img => {
			let speed = parseFloat(img.dataset.speed);
			let offset = window.scrollY * speed;

			img.style.transform = `translateY(${offset}px)`;
		});
	});



	// Create scroll-driven 360-degree rotation for each .tm-gsap-animate-circle
	document.querySelectorAll(".tm-gsap-animate-circle").forEach((circle, i) => {

		let spinAnim = gsap.timeline({
			scrollTrigger: {
				trigger: circle,
				scrub: true,
				start: "top bottom",
				end: "bottom top",
				toggleActions: "play none none reverse",
				markers: false
			}
		});

		spinAnim.fromTo(circle,
			{ rotation: 0, transformOrigin: "50% 50%" },
			{ rotation: 360, duration: 6, ease: "power1.out", immediateRender: false }
		);
	});




	// industries section scroll animation use this project home4
	const cards = document.querySelectorAll(".industries-card");
	if (cards.length >= 3) {
		gsap.set(cards[1], { y: 100 });
		gsap.set(cards[2], { y: 200 });

		const tl = gsap.timeline({
			scrollTrigger: {
				trigger: ".industries-wrapper",
				start: "top 150%",
				end: "bottom bottom",
				scrub: 0.5,
			}
		});

		// stagger ব্যবহার করে একসাথে animate করানো
		tl.to(cards, {
			y: 0,
			ease: "power2.out",
			duration: 0.5,
			stagger: 0.2 // প্রতিটি কার্ড 0.2s delay সহ শুরু হবে
		});
	}



	// service slider style use this project home one 
	let device_width = window.innerWidth;
	const serviceStack = gsap.utils.toArray(".service-item-pin");
	if (serviceStack.length > 0) {
		if (device_width > 992) {
			serviceStack.forEach(item => {
				gsap.to(item, {
					opacity: 0,
					scale: 0.9,
					y: 50,
					scrollTrigger: {
						trigger: item,
						scrub: true,
						start: "top 150px",
						pin: true,
						pinSpacing: false,
						markers: false,
					},
				});
			});
		}
	}






	document.addEventListener("DOMContentLoaded", function () {
		let device_width = window.innerWidth;
		const projectStack = gsap.utils.toArray(".project-item-pin");

		if (projectStack.length > 0) {
			if (device_width > 992) {
				projectStack.forEach(item => {
					gsap.to(item, {
						opacity: 0,
						scale: 0.9,
						y: 50,
						scrollTrigger: {
							trigger: item,
							scrub: true,
							start: "top 150px",
							pin: true,
							pinSpacing: false,
							markers: false,
						},
					});
				});
			}
		}
	});



	/* ------------- Gsap Js ------------- */
	function gsapController() {
		let mediaMatch = gsap.matchMedia();

		function rtlValue(value) {
			const isRTL = document.documentElement.dir === "rtl";
			return isRTL ? -value : value;
		}

	}

	/* Image Hover Effect Start */
	const dataItemHover = () => {
		const initHoverEffect = (container, images) => {
			const styleNumber = container.data("style") || "01";
			const displacementPath = `assets/images/displacement/${styleNumber}.webp`;

			// Initialize hover effect
			const hoverInstance = new hoverEffect({
				parent: container.get(0),
				intensity: container.data("intensity") || 0.2,
				speedIn: container.data("speedin") || 1,
				speedOut: container.data("speedout") || 1,
				image1: images.eq(0).attr("src"),
				image2: images.eq(0).attr("src"),
				displacementImage: displacementPath,
				imagesRatio: images[0].width / images[0].height,
				hover: false,
			});

			container.closest(".data-item-hover")
				.on("mouseenter", () => hoverInstance.next && hoverInstance.next())
				.on("mouseleave", () => hoverInstance.previous && hoverInstance.previous());
		};

		const setupHoverAnimations = () => {
			$(".data-img-hover").each(function () {
				const currentContainer = $(this);
				const imageElements = currentContainer.find("img");
				const firstImage = imageElements.eq(0);

				if (firstImage[0].complete) {
					initHoverEffect(currentContainer, imageElements);
				} else {
					firstImage.on("load", () => initHoverEffect(currentContainer, imageElements));
				}
			});
		};

		setupHoverAnimations();
	};



})(jQuery);
