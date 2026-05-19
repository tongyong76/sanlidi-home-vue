import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath } from 'url';
import path from 'path';

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '@views': resolvePath('src/views'),
      '@images': resolvePath('src/assets/images'),
      '@utils': resolvePath('src/utils'),
      '@stores': resolvePath('src/store'),
      '@plugins': resolvePath('src/plugins'),
      '@styles': resolvePath('src/assets/styles')
    }
  },
  css: {
    preprocessorOptions: {
      // sass variable and mixin
      scss: {
        api: 'modern-compiler',
        additionalData: `
          @use "@styles/variables.scss" as *; @use "@styles/mixin.scss" as *;
        `
      }
    },
    postcss: {
      plugins: [
        {
          postcssPlugin: 'internal:charset-removal',
          AtRule: {
            charset: (atRule) => {
              if (atRule.name === 'charset') {
                atRule.remove();
              }
            }
          }
        }
      ]
    }
  },
  server: {
    host: '0.0.0.0',
    port: 8080,
    cors: true
  },
  build: {
    chunkSizeWarningLimit: 1500
  }
});

function resolvePath(paths: string) {
  return path.resolve(__dirname, paths);
}
