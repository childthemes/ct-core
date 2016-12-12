module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

		watch: {
			css: {
				files: ['assets/css/*.css'],
				tasks: ['cssmin'],
			},
			js: {
				files: ['assets/js/*.js'],
				tasks: ['uglify'],
			},
			php: {
				files: ['**/*.php', '!node_modules/**', '!assets/**'],
				task: ['checktextdomain']
			}
		},

    uglify: {
      options: {
				mangle: false,
				sourceMap: false,
      	banner: '/*! ChldThemes Core <%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      dist: {
				files: [{
					expand: true,
          cwd: 'assets/js',
          src: ['*.js', '!*.min.js', '!shortcodes.js'],
          dest: 'assets/js',
					ext: '.min.js'
				}]
      }
    },

		cssmin: {
			build: {
				options: {
					rebase: true,
					keepSpecialComments: 0
				},
				files: [{
					expand: true,
					cwd: 'assets/css',
					src: ['*.css', '!*.min.css'],
					dest: './assets/css',
					ext: '.min.css'
				}]
			}
		},

		checktextdomain: {
			options:{
				force: false,
				text_domain: 'ctcore',
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
					'**/*.php',
					'!node_modules/**',
					'!assets/**'
				],
				expand: true
			}
		},

		makepot: {
			theme: {
				options: {
					cwd: './',
					potFilename: 'ctcore.pot',
					domainPath: '/languages',
					type: 'wp-plugin',
					mainFile: 'ct-core.php',
					updateTimestamp: true,
					exclude: [
						'node_modules/.*',
						'assets/.*'
					],
					potHeaders: {
						poedit: true,
						'last-translator': 'Rizal Fauzie <fauzie@childthemes.net>',
						'language-team': 'Child Themes <support@childthemes.net>',
						'plural-forms': 'nplurals=2; plural=(n != 1);',
						'language': 'en',
						'x-poedit-country': 'English',
						'x-poedit-language': 'United States',
						'x-poedit-sourcecharset': 'UTF-8',
						'x-poedit-basepath': '../',
						'x-poedit-searchpath-0': '.',
						'x-poedit-bookmarks': '',
						'x-textdomain-support': 'yes',
						'x-poedit-keywordslist': true
					}
				}
			}
		},

  });

  // Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-checktextdomain' );
  grunt.loadNpmTasks( 'grunt-wp-i18n' );

  // Default task(s).
  grunt.registerTask( 'default', 'watch' );
  grunt.registerTask( 'minify', ['uglify', 'cssmin'] );
  grunt.registerTask( 'translate', ['checktextdomain', 'makepot'] );

};
