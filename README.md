# Modularis Framework - Suivi de Développement

Ce dépôt contient le suivi de développement du framework Modularis, un framework modulaire pour applications web.

## Objectifs

- Créer un framework modulaire, robuste, et facile à utiliser.
- Assurer la maintenabilité et la testabilité de chaque composant.
- Optimiser chaque classe de base pour atteindre une performance maximale.

## Classes de Base et Statut

1. **Core**
   - **Description**: Classe centrale du framework, initialisant les composants globaux.
   - **Statut**: À faire
   - **Objectifs**: Initialiser tous les composants globaux, gérer la configuration et le chargement des modules.

2. **Env**
   - **Description**: Gère le chargement des variables d'environnement depuis un fichier `.env`.
   - **Statut**: Complété
   - **Objectifs**: Valider et charger correctement toutes les variables d'environnement.
   - **Tests**: Tests unitaires réalisés avec succès.

3. **Config**
   - **Description**: Gère la configuration de l'application.
   - **Statut**: À faire
   - **Objectifs**: Charger les fichiers de configuration, fournir un accès facile aux paramètres.

4. **Logger**
   - **Description**: Fournit des fonctionnalités de journalisation pour l'application.
   - **Statut**: Complété
   - **Objectifs**: Enregistrer les logs à différents niveaux (info, debug, error).
   - **Tests**: Tests unitaires réalisés avec succès.

5. **Database**
   - **Description**: Gère la connexion à la base de données et l'exécution des requêtes SQL.
   - **Statut**: Complété
   - **Objectifs**: Connexion sécurisée, gestion des transactions.
   - **Tests**: Tests unitaires réalisés avec succès.

6. **Cache**
   - **Description**: Fournit une abstraction pour la gestion du cache.
   - **Statut**: À faire
   - **Objectifs**: Stocker et récupérer les données en cache efficacement.

7. **Router**
   - **Description**: Gère le routage des requêtes HTTP.
   - **Statut**: Complété
   - **Objectifs**: Définir et gérer les routes efficacement.
   - **Tests**: Tests unitaires réalisés avec succès.

8. **Request**
   - **Description**: Gère les données de requête HTTP.
   - **Statut**: À faire
   - **Objectifs**: Fournir un accès sécurisé aux données de requête.

9. **Response**
   - **Description**: Gère la génération et l'envoi des réponses HTTP.
   - **Statut**: À faire
   - **Objectifs**: Générer des réponses adaptées à chaque situation.

10. **View**
    - **Description**: Gère le rendu des vues et l'intégration des templates.
    - **Statut**: À faire
    - **Objectifs**: Rendre les vues de manière performante et flexible.

11. **Session**
    - **Description**: Gère la gestion des sessions utilisateur.
    - **Statut**: À faire
    - **Objectifs**: Gestion sécurisée des sessions.

12. **Middleware**
    - **Description**: Fournit une gestion des middlewares.
    - **Statut**: Complété
    - **Objectifs**: Exécuter la logique intermédiaire de manière efficace.
    - **Tests**: Tests unitaires réalisés avec succès.

13. **Controller**
    - **Description**: Classe de base pour les contrôleurs de l'application.
    - **Statut**: À faire
    - **Objectifs**: Fournir une base commune pour la gestion des actions.

14. **ServiceManager**
    - **Description**: Gère les services de l'application.
    - **Statut**: À faire
    - **Objectifs**: Faciliter l'injection de dépendances et la gestion des services.

15. **ErrorManager**
    - **Description**: Gère la gestion des erreurs et des exceptions.
    - **Statut**: À faire
    - **Objectifs**: Gérer les erreurs de manière centralisée.

16. **Migration**
    - **Description**: Gère les migrations de base de données.
    - **Statut**: Complété
    - **Objectifs**: Créer, exécuter et annuler les migrations.
    - **Tests**: Tests unitaires réalisés avec succès.

## Notes de Développement

- Toutes les classes doivent être optimisées pour une utilisation maximale des ressources.
- Chaque classe doit être testée unitairement avant de passer à la suivante.
- Toute modification nécessaire à l'outil CLI doit être effectuée après la finalisation de chaque classe.

## Contribuer

Les contributions sont les bienvenues ! Veuillez soumettre des pull requests avec des descriptions claires de vos modifications.

## Licence

*Information sur la licence à définir ultérieurement.*
