package com.example.controller;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Slider;
import javafx.scene.control.TextField;

public class ConfigController {

    @FXML
    private TextField outField;

    @FXML
    private Slider timeSlider;

    @FXML
    private TextField activityMin;

    @FXML
    private TextField activityMax;

    @FXML
    private TextField humidityMin;

    @FXML
    private TextField humidityMax;

    @FXML
    private TextField coMin;

    @FXML
    private TextField coMax;

    @FXML
    private TextField illuminMin;

    @FXML
    private TextField illuminMax;

    @FXML
    private Button ModifOut;

    @FXML
    private Button cancel;

    @FXML
    private Button valid;

    @FXML
    private void modifOutField() {
        // Logique pour modifier le champ outField
    }

    @FXML
    private void cancel() {
        // Logique pour annuler les modifications
    }

    @FXML
    private void valid() {
        // Logique pour valider les modifications
    }
}