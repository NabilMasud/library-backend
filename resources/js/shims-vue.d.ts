declare module '*.vue' {
    import { DefineComponent } from 'vue';
    const component: DefineComponent<Record<string, any>, allowObjectTypes, any>;
    export default component;
}
