pipeline {
    agent any

    environment {
        DB_CONTAINER_NAME = 'mariadb-1'
        DB_ROOT_PASSWORD = 'root'
        DB_NAME = 'sf_testing'
    }

    stages {
        stage('Attendre la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Attendre la Base de Données"
                    def isRunning = sh(script: "docker ps --filter name=${DB_CONTAINER_NAME} --filter status=running --format '{{.Names}}'", returnStdout: true).trim()

                    if (isRunning != '') {
                        echo "------------------- Succès: Le conteneur MariaDB est en cours d'exécution."
                    } else {
                        error "------------------- Erreur: Le conteneur MariaDB n'est pas en cours d'exécution."
                    }
                }
            }
        }


        stage('Obtenir l\'Adresse IP de la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Obtenir l'Adresse IP de la Base de Données"
                    def dbIpAddress = sh(script: "docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ${DB_CONTAINER_NAME}", returnStdout: true).trim()
                    env.DB_IP_ADDRESS = dbIpAddress
                    echo "Adresse IP du conteneur MariaDB : ${env.DB_IP_ADDRESS}"
                    echo "------------------- Succès: Adresse IP obtenue."
                }
            }
        }

        stage('Tester la Connexion à la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Tester la Connexion à la Base de Données"
                    sh """
                        mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e "SHOW DATABASES;"
                    """
                    echo "------------------- Succès: Connexion à la base de données testée."
                }
            }
        }

        stage('Ajouter une Donnée') {
            steps {
                script {
                    echo "------------------- Étape: Ajouter une Donnée"
                    sh """
                        mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e "CREATE TABLE IF NOT EXISTS ${DB_NAME}.test_table (id INT PRIMARY KEY AUTO_INCREMENT, data VARCHAR(100));"
                        mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e "INSERT INTO ${DB_NAME}.test_table (data) VALUES ('exemple de donnée');"
                    """
                    echo "------------------- Succès: Donnée ajoutée à la table test_table."
                }
            }
        }

        stage('Logger la Donnée') {
            steps {
                script {
                    echo "------------------- Étape: Logger la Donnée"
                    def result = sh(script: "mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e \"SELECT * FROM ${DB_NAME}.test_table;\"", returnStdout: true).trim()
                    echo "Contenu de la table test_table :\n${result}"
                    echo "------------------- Succès: Donnée loguée."
                }
            }
        }

        stage('Supprimer la Donnée') {
            steps {
                script {
                    echo "------------------- Étape: Supprimer la Donnée"
                    sh """
                        mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e "DELETE FROM ${DB_NAME}.test_table WHERE data='exemple de donnée';"
                    """
                    echo "------------------- Succès: Donnée supprimée de la table test_table."
                }
            }
        }
    }

    post {
        always {
            script {
                echo "------------------- Pipeline terminé."
            }
        }
    }
}
