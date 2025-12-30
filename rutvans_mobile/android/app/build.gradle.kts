plugins {
    id("com.android.application")
    id("kotlin-android")
    // El plugin de Flutter debe ir al final
    id("dev.flutter.flutter-gradle-plugin")
}

android {

    namespace = "com.example.rutvans_mobile"
    compileSdk = flutter.compileSdkVersion
    ndkVersion = "27.0.12077973"

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_11
        targetCompatibility = JavaVersion.VERSION_11
    }

    kotlinOptions {
        jvmTarget = JavaVersion.VERSION_11.toString()
    }

    defaultConfig {
        applicationId = "com.example.rutvans_mobile"
        minSdk = flutter.minSdkVersion
        targetSdk = flutter.targetSdkVersion
        versionCode = flutter.versionCode
        versionName = flutter.versionName
    }

signingConfigs {
    create("release") {
        storeFile = file("rutvans-release-key.keystore")
        storePassword = "rutvans123"
        keyAlias = "rutvans"
        keyPassword = "rutvans123"
    }
}



    buildTypes {
        getByName("release") {
            isMinifyEnabled = true
            isShrinkResources = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
            signingConfig = signingConfigs.getByName("release")
        }
    }
}

flutter {
    source = "../.."
}
