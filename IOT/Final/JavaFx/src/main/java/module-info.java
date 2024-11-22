module com.example.sae_devapp {
    requires javafx.controls;
    requires javafx.fxml;


    opens com.example.sae_devapp to javafx.fxml;
    exports com.example.sae_devapp;
}