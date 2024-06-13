pipeline {
    agent {
        docker {
            image 'php:8.2'
            args '-u root'
        }
    }
    environment {
        COMPOSER_CACHE_DIR = '/var/jenkins_home/composer_cache'
    }
    stages {
        stage('Install Dependencies') {
            steps {
                sh 'apt-get update && apt-get install -y git unzip'
                sh 'docker-php-ext-install pdo pdo_mysql'
                sh 'curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer'
                sh 'composer install --prefer-dist --no-interaction'
            }
        }
        stage('Run Linter') {
            steps {
                sh './vendor/bin/php-cs-fixer fix --dry-run --diff'
            }
        }
        stage('Run Tests') {
            steps {
                sh './vendor/bin/phpunit'
            }
        }
    }
    post {
        always {
            junit 'tests/logs/junit.xml'
            archiveArtifacts artifacts: '**/build/logs/*.xml', allowEmptyArchive: true
        }
        success {
            echo 'Pipeline succeeded!'
        }
        failure {
            echo 'Pipeline failed!'
        }
    }
}
