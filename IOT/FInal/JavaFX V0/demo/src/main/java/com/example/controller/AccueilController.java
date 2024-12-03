package com.example.controller;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.stage.Stage;

import java.io.IOException;

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
        try {
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("/com/example/view/Config_UneDonnéeTousLesSalles.fxml"));
            Parent root = fxmlLoader.load();
            Stage stage = new Stage();
            stage.setTitle("Configuration");
            stage.setScene(new Scene(root));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void handleDonneesSallesButtonAction() {
        try {
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("/com/example/view/tabBord.fxml"));
            Parent root = fxmlLoader.load();
            Stage stage = new Stage();
            stage.setTitle("Configuration");
            stage.setScene(new Scene(root));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
        System.out.println("Données salles button clicked");
    }

    @FXML
    private void handleSolarButtonAction() {
        try {
            FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("/com/example/view/SolarEdge.fxml"));
            Parent root = fxmlLoader.load();
            Stage stage = new Stage();
            stage.setTitle("SolarEdge");
            stage.setScene(new Scene(root));
            stage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
        System.out.println("Solar button clicked");
    }
}