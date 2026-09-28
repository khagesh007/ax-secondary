import FolderPage from "../pages/documents/Index";

export default [
    {
        path: '/documents',
        name: 'documents',
        component: FolderPage,
    },
    {
        path: '/documents/:slug',
        name: 'documents-view',
        component: FolderPage,
    },
];
