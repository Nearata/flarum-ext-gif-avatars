import app from "flarum/admin/app";

app.initializers.add("nearata-gif-avatars", () => {
  app.extensionData
    .for("nearata-gif-avatars")
    .registerSetting({
      setting: "nearata-gif-avatars.image-optimizer",
      type: "select",
      label: app.translator.trans(
        "nearata-gif-avatars.admin.settings.image_optimizer.label"
      ),
      options: {
        none: app.translator.trans(
          "nearata-gif-avatars.admin.settings.image_optimizer.none_label"
        ),
        imageMagick: app.translator.trans(
          "nearata-gif-avatars.admin.settings.image_optimizer.image_magick_label"
        ),
      },
    })
    .registerPermission(
      {
        icon: "fas fa-user-tie",
        label: app.translator.trans(
          "nearata-gif-avatars.admin.permissions.use_gifs"
        ),
        permission: "nearata-gif-avatars.use-gifs",
      },
      "start"
    );
});
