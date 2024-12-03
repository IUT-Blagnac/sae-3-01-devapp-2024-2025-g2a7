package com.example.controller;

import java.io.IOException;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.stage.Stage;

public class SolarEdgeController {
    @FXML
    private Button btnRetour;

    @FXML
    private void ActionBtnRetour() {
        Stage stage = (Stage) btnRetour.getScene().getWindow();
    
    stage.close();

    }

}
