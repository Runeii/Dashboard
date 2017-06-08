module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    watch: {
      php: {
        files: ['**/*.php']
      },
      sass: {
        files: 'assets/sass/*.scss',
        tasks: ['sass:dev', 'notify:successCss'],
      },
      js: {
        files: [
          'assets/js/src/*.js',
          'Gruntfile.js'
        ],
        tasks: ['babel','concat','uglify:scripts','notify:successJs']
      }
    },
    php: {
      default : {
        options: {
				  hostname: '127.0.0.1',
        	port: 8010,
  				keepalive: false,
  				open: false
        }
      }
    },
    browserSync: {
      files: {
        src : [
          'assets/css/*.css',
          'assets/js/*.js',
          '**/*.html',
          '**/*.php'
        ],
      },
      options: {
        watchTask: true,
        notify: true,
        open: true,
        port: '8080',
				proxy: '<%= php.default.options.hostname %>:<%= php.default.options.port %>',
				ghostMode: {
					clicks: true,
					scroll: true,
					links: true,
					forms: true
				}
      }
    },
    sass: {
        options: {
            sourceMap: true
        },
        dev: {
            files: {
              'assets/css/style-min.css': 'assets/sass/screen.scss'
            }
        },
        dist: {
            files: {
              'assets/css/style.css': 'assets/sass/screen.scss'
            }
        }
    },
    postcss: {
      options: {
        map: false,
        processors: [
          require('autoprefixer')({browsers: ['last 1 version', '> 1%', 'ie 8']})
        ]
      },
      dist: {
        files: {
          'assets/css/style-prefixed.css': ['assets/css/style.css']
        }
      }
    },
    cssmin: {
      combine: {
        files: {
          'assets/css/style-min.css': ['assets/css/style-prefixed.css'/*, 'assets/css/vendors/*' */]
        },
      },
    },
    babel: {
        options: {
            presets: ['env']
        },
        dist: {
            files: {
                'assets/js/src/main-babel.js': 'assets/js/src/main.js'
            }
        }
    },
    concat: {
      options: {
        separator: ';',
        stripBanners: true
      },
      stageone: {
        src: ['assets/js/src/sources.js','assets/js/src/main-babel.js'],
        dest: 'assets/js/scripts-concat.js',
      }
    },
    uglify: {
      scripts: {
        files: {
          'assets/js/scripts-min.js': ['assets/js/scripts-concat.js']
        },
      },
    },
    notify: {
      options: {
        enabled: true,
        success: true,
        duration: 3
      },
      successCss: {
          options:{
              title: "Grunt successful",
              message: "All CSS tasks complete"
          }
      },
      successJs: {
          options:{
              title: "Grunt successful",
              message: "All JS tasks complete"
          }
      },
      successProduction: {
          options:{
              title: "Grunt successful",
              message: "Project prepared for production"
          }
      }
    }
  });

  // Server
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-php');
  grunt.loadNpmTasks('grunt-browser-sync');
  grunt.loadNpmTasks('grunt-notify');
  //CSS
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  //JS
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-babel');
  // Register the default tasks.

  grunt.registerTask('default', ['php', 'browserSync', 'watch', 'notify']);
  grunt.registerTask('prod', ['sass:dist', 'postcss', 'cssmin','babel','concat','uglify:scripts', 'notify:successProduction']);
};
