package com.example.controller;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;

public class AccueilController {

    @FXML
    private Button configurationButton;

    @FXML
    private Button donneesSallesButton;

    @FXML
    private Button solarButton;

    @FXML
    private Label accueilLabel;

    @FXML
    private void initialize() {
        // Initialisation du contrôleur, si nécessaire
    }

    @FXML
    private void handleConfigurationButtonAction() {
        // Logique pour le bouton "Configuration"
        System.out.println("Configuration button clicked");
    }

    @FXML
    private void handleDonneesSallesButtonAction() {
        // Logique pour le bouton "Données salles"
        System.out.println("Données salles button clicked");
    }

    @FXML
    private void handleSolarButtonAction() {
        // Logique pour le bouton "Solar"
        System.out.println("Solar button clicked");
    }
}