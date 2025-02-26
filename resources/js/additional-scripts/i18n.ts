import i18next from "i18next";
import lang_pl from "../../lang/pl.json";
// import lang_en from "../../lang/en.json";

i18next.init({
    lng: "pl",
    resources: {
        pl: {
            translation: lang_pl,
        },
        // en: {
        //     translation: lang_en,
        // },
    },
});

export const getTranslation = (key: string): string => {
    return i18next.t(key);
};
