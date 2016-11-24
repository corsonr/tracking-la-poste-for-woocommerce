/* jshint node:true */
module.exports = function( grunt ){
	'use strict';

	grunt.initConfig({
		
		pkg: grunt.file.readJSON('package.json'),
		
		// setting folder templates
		dirs: {
			css: 'assets/css',
			js: 'assets/js'
		},

		// Compile all .scss files.
		sass: {
			compile: {
				options: {
					sourcemap: 'none'
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.css %>/',
					src: ['*.scss'],
					dest: '<%= dirs.css %>/',
					ext: '.css'
				}]
			}
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/',
				src: ['*.css'],
				dest: '<%= dirs.css %>/',
				ext: '.css'
			}
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: 'some'
			},
			jsfiles: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/',
					src: [
						'*.js',
						'!*.min.js',
						'!Gruntfile.js',
					],
					dest: '<%= dirs.js %>/',
					ext: '.min.js'
				}]
			}
		},

		// Watch changes for assets
		watch: {
			css: {
				files: ['<%= dirs.css %>/*.scss'],
				tasks: ['sass', 'cssmin'],
			},
			js: {
				files: [
					'<%= dirs.js %>/*js',
					'!<%= dirs.js %>/*.min.js'
				],
				tasks: ['uglify']
			}
		},
		
		// Create .pot files
		makepot: {
			dist: {
				options: {
					domainPath: '/languages/',
					exclude: [
								'node_modules/.*',
								'build/.*'
							],
					potFilename: 'tracking-la-poste-for-woocommerce.pot',
					type: 'wp-plugin',
					potHeaders: {
					    'language': 'en',
					    'plural-forms': 'nplurals=2; plural=(n != 1);',
					    'x-poedit-country': 'United States',
					    'x-poedit-sourcecharset': 'UTF-8',
					    'x-poedit-keywordslist': '__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c;_nc:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;',
					    'x-poedit-basepath': '../',
					    'x-poedit-searchpath-0': '.',
					    'x-poedit-bookmarks': '',
					    'x-textdomain-support': 'yes',
						'report-msgid-bugs-to': 'https://remicorson.com',
						'language-team': 'YOUR TEAM <YOUR-EMAIL@ADDRESS>'
					}
				}
			}
		},

		// Check the textdomain
		checktextdomain: {
			options:{
				text_domain: 'tracking-la-poste-for-woocommerce',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php', // Include all files
					'!node_modules/**', // Exclude node_modules/
					'!build/.*', // Exclude build/
					'!.sass-cache/.*' // Exclude build/
				],
				expand: true
			}
		},
		
		// Clean up build directory
		clean: {
			main: ['build/<%= pkg.name %>']
		},
		
		// Copy into the build directory
		copy: {
		  main: {
		    expand: true,
		    src: [
					'**',
					'!node_modules/**',
					'!build/**',
					'!.sass-cache/**',
					'!Gruntfile.js',
					'!package.json',
					'!**/Gruntfile.js',
					'!**/package.json',
					'!<%= dirs.css %>/*.scss',
					'!**/*~'
				],
		    dest: 'build/<%= pkg.name %>/',
		  },
		 },
		
		//Compress build directory into <name>.zip and <name>-<version>.zip
		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		},

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );

	// Register tasks
	grunt.registerTask( 'default', [
		'sass',
		'cssmin',
		'uglify'
	] );

	// Just an alias for pot file generation
	grunt.registerTask( 'pot', [
		'makepot'
	] );
	
	// Build task(s).
	grunt.registerTask( 'build', [ 
		'clean', 
		'copy',
		'compress',
		'build-clean' 
	] );
	
	// Build-clean task(s).
	grunt.registerTask( 'build-clean', [ 
		'clean'
	] );

};
