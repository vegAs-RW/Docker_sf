pipeline {
    agent {
        docker {
            image 'php:8.2'
            args '-u root'
        }
    }
    environment {
        COMPOSER_CACHE_DIR = '/var/jenkins_home/composer_cache'
        DB_HOST = 'mariadb'
        DB_PORT = '3306'
        DB_NAME = 'sf_testing'
        DB_USER = 'root'
        DB_PASSWORD = 'root'
        DB_SERVER_VERSION = '10.5'  // Version MariaDB compatible
        DB_CHARSET = 'utf8mb4'
    }
    stages {
        stage('Install Dependencies') {
            steps {
                sh '''
                apt-get update && \
                apt-get install -y git unzip default-mysql-client wget && \
                docker-php-ext-install pdo pdo_mysql && \
                curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
                wget https://github.com/jwilder/dockerize/releases/download/v0.6.1/dockerize-linux-amd64-v0.6.1.tar.gz && \
                tar -C /usr/local/bin -xzvf dockerize-linux-amd64-v0.6.1.tar.gz && \
                composer install --prefer-dist --no-interaction
                '''
            }
        }
        stage('Create .env.test.local') {
            steps {
                script {
                    def dbUrl = "mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}?serverVersion=${DB_SERVER_VERSION}&charset=${DB_CHARSET}"
                    writeFile file: '.env.test.local', text: "DATABASE_URL=\"${dbUrl}\"\n"
                }
            }
        }
        stage('Setup Database') {
            steps {
                script {
                    // Attendre que MariaDB soit prêt
                    sh 'dockerize -wait tcp://mariadb:3306 -timeout 1m'
                    // Exécuter les commandes SQL nécessaires pour configurer la base de données
                    sh 'mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"'
                }
            }
        }
        stage('Run Linter') {
            steps {
                sh './vendor/bin/php-cs-fixer fix --dry-run --diff'
            }
        }
        stage('Run Tests') {
            steps {
                sh 'APP_ENV=test ./vendor/bin/phpunit'
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
