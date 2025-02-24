<template>
  <div class="py-3 text-center">
    <h2 class="main-title tx-dark fw-bold">Contact Us</h2>
    <p class="fs-20 mt-30 lg-mt-20">Get our all info and also can message us directly from here</p>
  </div>
  <div>
    <div class="card  rounded-3 my-auto">

      <div class="card-body p-4">
        <form @submit.prevent="submitForm">
          <div class="row controls">
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.name" label="Name" name="name" placeholder="Enter your names" maxlength="10"
                :error="errors.name?._errors?.[0]" />
            </div>
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.email" label="Email" name="email" placeholder="Enter your email address"
                :error="errors.email?._errors?.[0]" />
            </div>
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.phone" label="Phone" name="phone" placeholder="eg: +1000000"
                :error="errors.phone?._errors?.[0]" />
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label class="form-label">Message:</label>
                <textarea v-model="form.message" placeholder="Enter your message" class="form-control"
                  :class="{ 'is-invalid': errors.message }"></textarea>
                <p v-if="errors.message" class="invalid-feedback text-xs mb-0">{{ errors.message._errors[0] }}
                </p>
              </div>
            </div>
          </div>
          <div class="relative my-4">
            <div class="position-relative text-center">
              <hr class="border border-secondary">
              <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white">
                ADDRESS
              </span>
            </div>
          </div>
          <div class="row controls">
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.street" label="Street" name="street" placeholder="Enter your address"
                :error="errors.street?._errors?.[0]" />
            </div>
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.state" label="Street" name="state" placeholder="State"
                :error="errors.state?._errors?.[0]" />
            </div>
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.zip" label="Zip" name="zip" placeholder="Zip code"
                :error="errors.zip?._errors?.[0]" />
            </div>
            <div class="col-12 col-sm-6">
              <BaseInput v-model="form.country" label="Country" name="country" placeholder="Country"
                :error="errors.country?._errors?.[0]" />
            </div>
          </div>

          <div class="relative my-4">
            <div class="position-relative text-center">
              <hr class="border border-secondary">
              <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white">
                UPLOADS
              </span>
            </div>
          </div>
          <div class="row controls mb-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">Image (JPG only):</label>
              <input type="file" ref="imageInput" class="form-control" :class="{ 'is-invalid': errors.image }"
                @change="(e) => (form.image = e.target.files[0])" accept="image/jpeg" />
              <p v-if="errors.image" class="invalid-feedback text-xs mb-0">{{ errors.image._errors[0] }}</p>
            </div>
          </div>

          <div class="row controls mb-3">
            <div class="col-12 col-sm-6">
              <label class="form-label">File (PDF only):</label>
              <input type="file" ref="fileInput" class="form-control" :class="{ 'is-invalid': errors.file }"
                @change="(e) => (form.file = e.target.files[0])" accept="application/pdf" />
              <p v-if="errors.file" class="invalid-feedback text-xs mb-0">{{ errors.file._errors[0] }}</p>
            </div>
          </div>

          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="alert alert-danger">
            {{ errorMessage }}
          </div>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
            Submit
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { z } from "zod";
import axios from "axios";
import BaseInput from "./BaseInput.vue";


const schema = z.object({
  name: z.string().min(2, "Name must be at least 2 characters").max(10, "Name must be max 10 characters"),
  email: z.string().email("Invalid email format").refine(
    (value) => !/^[^@]+@gmail\.com$/.test(value),
    "Gmail is not allowed"
  ),
  phone: z.string().regex(/^\+\d{1,3}\d{9,}$/, "Invalid phone format"),
  message: z.string().min(10, "Message must be at least 10 characters"),
  street: z.string().optional(),
  state: z.string().optional(),
  zip: z.string().optional(),
  country: z.string().optional(),
  image: z
    .any()
    .refine((file) => file instanceof File, "Image is required")
    .refine((file) => file && file.type === "image/jpeg", "Only JPG images allowed"),
  file: z
    .any()
    .refine((file) => file instanceof File, "File (.pdf) is required")
    .refine((file) => file && file.type === "application/pdf", "Only PDF files allowed"),
});

const form = ref({
  name: "",
  email: "",
  phone: "",
  message: "",
  street: "",
  state: "",
  zip: "",
  country: "",
  image: null,
  file: null,
});

const errors = ref({});
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

const hasImage = computed(() => !!form.value.image);
const hasFile = computed(() => !!form.value.file);
const imageInput = ref(null);
const fileInput = ref(null);

const validateForm = () => {
  const result = schema.safeParse(form.value);
  errors.value = result.success ? {} : result.error.format();
  return result.success;
};

const submitForm = async () => {
  if (!validateForm()) return;
  successMessage.value = "";
  errorMessage.value = "";
  loading.value = true;

  const formData = new FormData();
  Object.entries(form.value).forEach(([key, value]) => {
    if (value) formData.append(key, value);
  });

  try {
    await axios.post("/api/submission", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    successMessage.value = "Submission successful!";
    // Reset form
    form.value = {};
    if (imageInput.value) imageInput.value.value = "";
    if (fileInput.value) fileInput.value.value = "";
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Submission failed.";
  } finally {
    loading.value = false;
  }
};
</script>